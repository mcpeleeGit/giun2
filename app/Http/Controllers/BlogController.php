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

        if ($title === '' || $content === '') {
            flash('blog_error', '제목과 내용을 모두 입력해 주세요.');
            redirect('/blog');
        }

        $post = new Post();
        $post->title = $title;
        $post->content = $content;
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

        if ($title === '' || $content === '') {
            flash('blog_error', '제목과 내용을 모두 입력해 주세요.');
            redirect('/blog');
        }

        $post = new Post();
        $post->id = (int)$id;
        $post->title = $title;
        $post->content = $content;
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
}
