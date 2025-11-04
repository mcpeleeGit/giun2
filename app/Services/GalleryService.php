<?php

namespace App\Services;

use App\Models\Gallery;
use App\Repositories\GalleryRepository;
use finfo;

class GalleryService
{
    private const MAX_IMAGE_SIZE = 5 * 1024 * 1024; // 5MB
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    protected GalleryRepository $galleryRepository;

    public function __construct()
    {
        $this->galleryRepository = new GalleryRepository();
    }

    public function getAll()
    {
        return $this->galleryRepository->getAll();
    }

    public function getById($id)
    {
        return $this->galleryRepository->getById($id);
    }

    public function createItem(Gallery $gallery, ?array $image): bool
    {
        if (!$this->validateGallery($gallery) || !$this->validateImage($image, true)) {
            return false;
        }

        $imagePath = $this->uploadImage($image);
        if ($imagePath === null) {
            return false;
        }

        $gallery->image_path = $imagePath;

        return $this->galleryRepository->create($gallery);
    }

    public function update(Gallery $gallery, ?array $image): bool
    {
        if ($gallery->id === null || !$this->validateGallery($gallery)) {
            return false;
        }

        $existing = $this->galleryRepository->getById($gallery->id);
        if (!$existing) {
            return false;
        }

        $gallery->image_path = $existing->image_path;

        if ($image && ($image['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            if (!$this->validateImage($image, true)) {
                return false;
            }

            $imagePath = $this->uploadImage($image);
            if ($imagePath === null) {
                return false;
            }

            $gallery->image_path = $imagePath;
        }

        return $this->galleryRepository->update($gallery);
    }

    private function uploadImage(array $image): ?string
    {
        $extension = strtolower(pathinfo($image['name'] ?? '', PATHINFO_EXTENSION));
        if (!in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            return null;
        }

        $uploadDir = '/assets/uploads/gallery';
        // 프로젝트 루트를 기준으로 경로 계산 (index.php가 있는 디렉토리)
        $projectRoot = dirname(__DIR__, 2);
        $uploadPath = $projectRoot . $uploadDir;

        if (!is_dir($uploadPath) && !mkdir($uploadPath, 0755, true) && !is_dir($uploadPath)) {
            error_log('❌ 업로드 디렉터리를 생성할 수 없습니다: ' . $uploadPath);
            return null;
        }

        if (!is_writable($uploadPath)) {
            error_log('❌ 업로드 경로에 쓰기 권한이 없습니다: ' . $uploadPath);
            return null;
        }

        try {
            $filename = time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
        } catch (\Exception $e) {
            error_log('❌ 파일명을 생성할 수 없습니다: ' . $e->getMessage());
            return null;
        }
        $destination = $uploadPath . '/' . $filename;

        if (!move_uploaded_file($image['tmp_name'], $destination)) {
            error_log('❌ 이미지 업로드에 실패했습니다.');
            return null;
        }

        return $uploadDir . '/' . $filename;
    }

    private function validateImage(?array $image, bool $required = false): bool
    {
        if ($image === null || ($image['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return !$required;
        }

        if (($image['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            return false;
        }

        if (($image['size'] ?? 0) > self::MAX_IMAGE_SIZE) {
            return false;
        }

        $tmpName = $image['tmp_name'] ?? '';
        if (!is_uploaded_file($tmpName)) {
            return false;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($tmpName);
        if ($mimeType === false || !in_array($mimeType, self::ALLOWED_MIME_TYPES, true)) {
            return false;
        }

        return true;
    }

    private function validateGallery(Gallery $gallery): bool
    {
        $gallery->title = trim($gallery->title ?? '');
        $gallery->author = trim($gallery->author ?? '');
        $gallery->description = trim($gallery->description ?? '');

        if ($gallery->title === '' || mb_strlen($gallery->title) > 120) {
            return false;
        }

        if ($gallery->author === '' || mb_strlen($gallery->author) > 80) {
            return false;
        }

        if (mb_strlen($gallery->description) > 1000) {
            $gallery->description = mb_substr($gallery->description, 0, 1000);
        }

        return true;
    }
}
