<?php
namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\BlogService;
use Exception;

class BlogController
{
    protected $service;

    public function __construct()
    {
        $this->service = new BlogService();
    }

    public function index()
    {
        $user = require_login();
        $posts = $this->service->getForUser($user->id, $user->name);

        view('blog/index', [
            'currentUser' => $user,
            'posts' => $posts,
            'message' => flash('blog_message'),
            'error' => flash('blog_error'),
        ]);
    }

    public function store()
    {
        $user = require_login();
        require_csrf_token($_POST['csrf_token'] ?? null);

        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $sanitizedContent = sanitize_rich_text($content);

        if ($title === '' || $sanitizedContent === '') {
            flash('blog_error', '제목과 내용을 모두 입력해 주세요.');
            redirect('/blog');
        }

        $post = new Post();
        $post->title = $title;
        $post->content = $sanitizedContent;
        $post->author = $user->name;
        $post->user_id = $user->id;

        if ($this->service->createPost($post)) {
            flash('blog_message', '새로운 블로그 글이 작성되었습니다.');
        } else {
            flash('blog_error', '블로그 글을 저장하지 못했습니다. 잠시 후 다시 시도해 주세요.');
        }

        redirect('/blog');
    }

    public function update($id)
    {
        $user = require_login();
        require_csrf_token($_POST['csrf_token'] ?? null);

        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $sanitizedContent = sanitize_rich_text($content);

        if ($title === '' || $sanitizedContent === '') {
            flash('blog_error', '제목과 내용을 모두 입력해 주세요.');
            redirect('/blog');
        }

        $post = new Post();
        $post->id = (int)$id;
        $post->title = $title;
        $post->content = $sanitizedContent;
        $post->author = $user->name;
        $post->user_id = $user->id;

        if ($this->service->updatePostForUser($post, $user->name)) {
            flash('blog_message', '블로그 글이 수정되었습니다.');
        } else {
            flash('blog_error', '블로그 글을 수정할 수 없습니다. 다시 시도해 주세요.');
        }

        redirect('/blog');
    }

    public function delete($id)
    {
        $user = require_login();
        require_csrf_token($_POST['csrf_token'] ?? null);

        if ($this->service->deletePostForUser((int)$id, $user->id, $user->name)) {
            flash('blog_message', '블로그 글이 삭제되었습니다.');
        } else {
            flash('blog_error', '블로그 글을 삭제할 수 없습니다.');
        }

        redirect('/blog');
    }

    public function show($id)
    {
        $user = require_login();
        $post = $this->service->getById($id);

        if (!$post || !$this->ownsPost($post, $user)) {
            flash('blog_error', '존재하지 않거나 접근할 수 없는 글입니다.');
            redirect('/blog');
        }

        return view('blog/detail', [
            'post' => $post,
            'currentUser' => $user,
        ]);
    }

    public function getById($id)
    {
        try {
            $post = $this->service->getById($id);
            if (!$post) {
                http_response_code(404);
                echo json_encode(['error' => 'Post not found']);
                return;
            }

            $user = current_user();
            $isAdmin = is_object($user) && (($user->role ?? null) === 'ADMIN');

            if (!$isAdmin && (!$user || !$this->ownsPost($post, $user))) {
                http_response_code(403);
                echo json_encode(['error' => 'Forbidden']);
                return;
            }

            echo json_encode($post);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'An error occurred']);
        }
    }

    private function ownsPost($post, $user): bool
    {
        if ($post === null || $user === null) {
            return false;
        }

        if (isset($post->user_id) && $post->user_id !== null) {
            return (int)$post->user_id === (int)$user->id;
        }

        return ($post->author ?? '') === ($user->name ?? '');
    }

    public function uploadImage(): void
    {
        $user = require_login();

        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['csrf_token'] ?? null);
        if (!validate_csrf_token($token)) {
            $this->respondJson(419, ['error' => '잘못된 요청입니다.']);
            return;
        }

        $file = $_FILES['image'] ?? null;
        if (!$file || !is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $this->respondJson(400, ['error' => '업로드할 이미지를 찾을 수 없습니다.']);
            return;
        }

        if (!is_uploaded_file($file['tmp_name'])) {
            $this->respondJson(400, ['error' => '잘못된 업로드 요청입니다.']);
            return;
        }

        $maxSize = 5 * 1024 * 1024; // 5MB
        if (($file['size'] ?? 0) > $maxSize) {
            $this->respondJson(413, ['error' => '이미지 크기는 5MB를 초과할 수 없습니다.']);
            return;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']) ?: '';
        $finfo = null;

        $allowedMimeTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
        ];

        if (!isset($allowedMimeTypes[$mimeType])) {
            $this->respondJson(415, ['error' => '지원하지 않는 이미지 형식입니다.']);
            return;
        }

        $projectRoot = dirname(__DIR__, 3);
        $uploadDir = '/assets/uploads/blog';
        $uploadPath = $projectRoot . $uploadDir;

        if (!is_dir($uploadPath) && !mkdir($uploadPath, 0755, true) && !is_dir($uploadPath)) {
            $this->respondJson(500, ['error' => '이미지를 저장할 수 없습니다.']);
            return;
        }

        try {
            $randomName = bin2hex(random_bytes(16));
        } catch (\Exception $exception) {
            $this->respondJson(500, ['error' => '이미지 파일 이름을 생성할 수 없습니다.']);
            return;
        }

        $filename = sprintf('blog_%s.%s', $randomName, $allowedMimeTypes[$mimeType]);
        $destination = $uploadPath . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            $this->respondJson(500, ['error' => '이미지 업로드에 실패했습니다.']);
            return;
        }

        $imageUrl = $uploadDir . '/' . $filename;
        $this->respondJson(201, ['url' => $imageUrl]);
    }

    private function respondJson(int $status, array $payload): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    }
}
