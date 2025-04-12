<?php
namespace App\Repositories;

use App\Repositories\Common\Repository;
class BlogRepository extends Repository {
    
    public function __construct() {
        parent::__construct(); // 부모 클래스의 생성자 호출
    }

    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT id, title, created_at FROM blog_posts ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM blog_posts WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
