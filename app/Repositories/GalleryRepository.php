<?php

namespace App\Repositories;

use App\Repositories\Common\Repository;
use App\Models\Gallery;
use PDOException;

class GalleryRepository extends Repository {
    public function getAll() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM gallery_items ORDER BY created_at DESC");
            $galleryData = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $galleryItems = [];

            foreach ($galleryData as $data) {
                $gallery = new Gallery();
                $galleryItems[] = $this->mapDataToObject($data, $gallery);
            }

            return $galleryItems;
        } catch (PDOException $e) {
            error_log('GalleryRepository::getAll error: ' . $e->getMessage());
            return [];
        }
    }

    public function getById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM gallery_items WHERE id = ?");
            $stmt->execute([$id]);
            $galleryData = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($galleryData) {
                $gallery = new Gallery();
                return $this->mapDataToObject($galleryData, $gallery);
            }

            return null;
        } catch (PDOException $e) {
            error_log('GalleryRepository::getById error: ' . $e->getMessage());
            return null;
        }
    }

    public function create($gallery) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO gallery_items (title, description, image_path, author, created_at) VALUES (?, ?, ?, ?, NOW())");
            return $stmt->execute([$gallery->title, $gallery->description, $gallery->image_path, $gallery->author]);
        } catch (PDOException $e) {
            error_log('GalleryRepository::create error: ' . $e->getMessage());
            return false;
        }
    }

    public function update($gallery) {
        try {
            $stmt = $this->pdo->prepare("UPDATE gallery_items SET title = ?, description = ?, image_path = ?, author = ? WHERE id = ?");
            return $stmt->execute([$gallery->title, $gallery->description, $gallery->image_path, $gallery->author, $gallery->id]);
        } catch (PDOException $e) {
            error_log('GalleryRepository::update error: ' . $e->getMessage());
            return false;
        }
    }

}