<?php

namespace App\Repositories;

use App\Repositories\Common\Repository;
use App\Models\Gallery;

class GalleryRepository extends Repository {
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM gallery_items ORDER BY created_at DESC");
        $galleryData = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $galleryItems = [];

        foreach ($galleryData as $data) {
            $gallery = new Gallery();
            $galleryItems[] = $this->mapDataToObject($data, $gallery);
        }

        return $galleryItems;
    }

    public function create($gallery) {
        $stmt = $this->pdo->prepare("INSERT INTO gallery_items (title, description, image_path, author, created_at) VALUES (?, ?, ?, ?, NOW())");
        return $stmt->execute([$gallery->title, $gallery->description, $gallery->image_path, $gallery->author]);
    }
} 