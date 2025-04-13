<?php

namespace App\Services;

use App\Repositories\GalleryRepository;
use App\Models\Gallery;

class GalleryService {
    protected $galleryRepository;

    public function __construct() {
        $this->galleryRepository = new GalleryRepository();
    }

    public function getAll() {
        return $this->galleryRepository->getAll();
    }

    public function getById($id) {
        return $this->galleryRepository->getById($id);
    }

    public function createItem($gallery, $image) {

        if (!$this->validateImage($image)) {
            exit;
        }
        if (!$this->validateGallery($gallery)) {
            exit; 
        }
        $imagePath = $this->uploadImage($image);
        if (!$imagePath) {
            exit;
        }
        $gallery->image_path = $imagePath;
        
        return $this->galleryRepository->create($gallery);
    }

    public function update($gallery, $image) {

        if (!$this->validateId($gallery)) {
            exit;
        }
        if (!$this->validateImage($image)) {
            exit;
        }
        if (!$this->validateGallery($gallery)) {
            exit; 
        }
        $imagePath = $this->uploadImage($image);
        if (!$imagePath) {
            exit;
        }
        $gallery->image_path = $imagePath;

        return $this->galleryRepository->update($gallery);
    }

    private function uploadImage($image) {
        $uploadDir = '/assets/uploads/gallery/';
        $uploadPath = $_SERVER['DOCUMENT_ROOT'] . $uploadDir;

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        if (!is_writable($uploadPath)) {
            echo "❌ 업로드 경로에 쓰기 권한이 없습니다.";
        }

        $timestamp = time();
        $fileExtension = pathinfo($image['name'], PATHINFO_EXTENSION);
        $fileName = $timestamp . '.' . $fileExtension;
        $filePath = $uploadPath . $fileName;
        
        if (move_uploaded_file($image['tmp_name'], $filePath)) {
            return $uploadDir . $fileName;
        } else {
            echo "<h2>이미지 업로드에 실패했습니다.</h2>";
            return false;
        }        
    }

    private function validateImage($image) {
        if ($image['error'] !== UPLOAD_ERR_OK) {
            switch ($image['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    echo "<h2>파일이 너무 큽니다.</h2>";
                    return false;
                case UPLOAD_ERR_PARTIAL:
                    echo "<h2>파일이 부분적으로만 업로드되었습니다.</h2>";
                    return false;
                case UPLOAD_ERR_NO_FILE:
                    echo "<h2>파일이 업로드되지 않았습니다.</h2>";
                    return false;
                case UPLOAD_ERR_NO_TMP_DIR:
                    echo "<h2>임시 폴더가 없습니다.</h2>";
                    return false;
                case UPLOAD_ERR_CANT_WRITE:
                    echo "<h2>디스크에 파일을 쓸 수 없습니다.</h2>";
                    return false;
                case UPLOAD_ERR_EXTENSION:
                    echo "<h2>확장에 의해 파일 업로드가 중지되었습니다.</h2>";
                    return false;
                default:
                    echo "<h2>알 수 없는 오류가 발생했습니다.</h2>";
                    return false;
            }
        }
        return true;
    }

    private function validateGallery($gallery) {
        if (!$gallery->title || !$gallery->author) {
            echo "<h2>모든 필드를 입력해야 합니다.</h2>";
            return false;
        }
        return true;
    }

    private function validateId($gallery) {
        if (!$gallery->id) {
            echo "<h2>ID가 없습니다.</h2>";
            return false;
        }
        return true;
    }
} 