<?php
namespace App\Repositories;

use App\Repositories\Common\Repository;
use App\Models\Post;

class BlogRepository extends Repository {

    private ?bool $hasUserIdColumn = null;

    public function __construct() {
        parent::__construct(); // 부모 클래스의 생성자 호출
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM blog_posts ORDER BY created_at DESC");
        $postsData = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $posts = [];

        foreach ($postsData as $postData) {
            $post = new Post();
            $posts[] = $this->mapDataToObject($postData, $post);
        }

        return $posts;
    }

    public function findBetween(string $startDate, string $endDate): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM blog_posts WHERE created_at BETWEEN ? AND ? ORDER BY created_at ASC");
        $stmt->execute([$startDate, $endDate]);
        $postsData = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $postData) {
            $post = new Post();
            return $this->mapDataToObject($postData, $post);
        }, $postsData);
    }

    public function findBetweenByUser(int $userId, string $authorName, string $startDate, string $endDate): array
    {
        if ($this->supportsUserIdColumn()) {
            $stmt = $this->pdo->prepare("SELECT * FROM blog_posts WHERE user_id = ? AND created_at BETWEEN ? AND ? ORDER BY created_at ASC");
            $stmt->execute([$userId, $startDate, $endDate]);
        } else {
            $stmt = $this->pdo->prepare("SELECT * FROM blog_posts WHERE author = ? AND created_at BETWEEN ? AND ? ORDER BY created_at ASC");
            $stmt->execute([$authorName, $startDate, $endDate]);
        }

        $postsData = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $postData) {
            $post = new Post();
            return $this->mapDataToObject($postData, $post);
        }, $postsData);
    }

    public function findByUser(int $userId, string $authorName): array
    {
        if ($this->supportsUserIdColumn()) {
            $stmt = $this->pdo->prepare("SELECT * FROM blog_posts WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->execute([$userId]);
        } else {
            $stmt = $this->pdo->prepare("SELECT * FROM blog_posts WHERE author = ? ORDER BY created_at DESC");
            $stmt->execute([$authorName]);
        }

        $postsData = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return array_map(function (array $postData) {
            $post = new Post();
            return $this->mapDataToObject($postData, $post);
        }, $postsData);
    }

    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM blog_posts WHERE id = ?");
        $stmt->execute([$id]);
        $postData = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($postData) {
            $post = new Post();
            return $this->mapDataToObject($postData, $post);
        }

        return null;
    }

    public function create(Post $post): bool
    {
        if ($this->supportsUserIdColumn()) {
            $stmt = $this->pdo->prepare("INSERT INTO blog_posts (user_id, title, author, content, created_at) VALUES (?, ?, ?, ?, NOW())");
            return $stmt->execute([$post->user_id, $post->title, $post->author, $post->content]);
        }

        $stmt = $this->pdo->prepare("INSERT INTO blog_posts (title, author, content, created_at) VALUES (?, ?, ?, NOW())");
        return $stmt->execute([$post->title, $post->author, $post->content]);
    }

    public function update(Post $post): bool
    {
        $stmt = $this->pdo->prepare("UPDATE blog_posts SET title = ?, author = ?, content = ? WHERE id = ?");
        return $stmt->execute([$post->title, $post->author, $post->content, $post->id]);
    }

    public function updateForUser(Post $post, string $authorName): bool
    {
        if ($this->supportsUserIdColumn()) {
            $stmt = $this->pdo->prepare("UPDATE blog_posts SET title = ?, author = ?, content = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$post->title, $post->author, $post->content, $post->id, $post->user_id]);
            return $stmt->rowCount() > 0;
        }

        $stmt = $this->pdo->prepare("UPDATE blog_posts SET title = ?, author = ?, content = ? WHERE id = ? AND author = ?");
        $stmt->execute([$post->title, $post->author, $post->content, $post->id, $authorName]);
        return $stmt->rowCount() > 0;
    }

    public function delete($id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM blog_posts WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function deleteForUser(int $id, int $userId, string $authorName): bool
    {
        if ($this->supportsUserIdColumn()) {
            $stmt = $this->pdo->prepare("DELETE FROM blog_posts WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $userId]);
            return $stmt->rowCount() > 0;
        }

        $stmt = $this->pdo->prepare("DELETE FROM blog_posts WHERE id = ? AND author = ?");
        $stmt->execute([$id, $authorName]);
        return $stmt->rowCount() > 0;
    }

    private function supportsUserIdColumn(): bool
    {
        if ($this->hasUserIdColumn !== null) {
            return $this->hasUserIdColumn;
        }

        try {
            $stmt = $this->pdo->prepare("SHOW COLUMNS FROM blog_posts LIKE 'user_id'");
            $stmt->execute();
            $this->hasUserIdColumn = $stmt->fetch() !== false;
        } catch (\PDOException $e) {
            // 테이블이 없거나 접근할 수 없는 경우에는 user_id 컬럼이 없다고 간주합니다.
            $this->hasUserIdColumn = false;
        }

        return $this->hasUserIdColumn;
    }
}
