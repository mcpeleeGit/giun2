<?php
namespace App\Repositories;

use App\Repositories\Common\Repository;
use App\Models\Post;

class BlogRepository extends Repository {
    
    public function __construct() {
        parent::__construct(); // 부모 클래스의 생성자 호출
    }

    public function getAll()
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

    public function create($post) {
        $stmt = $this->pdo->prepare("INSERT INTO blog_posts (title, author, content, created_at) VALUES (?, ?, ?, NOW())");
        return $stmt->execute([$post->title, $post->author, $post->content]);
    }

    public function update($post) {
        $stmt = $this->pdo->prepare("UPDATE blog_posts SET title = ?, author = ?, content = ? WHERE id = ?");
        return $stmt->execute([$post->title, $post->author, $post->content, $post->id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM blog_posts WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
