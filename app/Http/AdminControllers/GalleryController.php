<?php

namespace App\Http\AdminControllers;

use App\Services\GalleryService;
use App\Http\AdminControllers\Common\Controller;
use App\Models\Gallery;

class GalleryController extends Controller {
    private $galleryService;

    public function __construct() {
        $this->galleryService = new GalleryService();
    }

    public function index() {
        // 갤러리 항목 목록 가져오기
        $galleryItems = $this->galleryService->getAll();

        // 갤러리 항목 목록을 뷰에 전달
        adminView('gallery', ['galleryItems' => $galleryItems]);
    }

    public function create() {
        $gallery = new Gallery();
        $this->mapRequestToObject($gallery, $_POST);
        $image = $_FILES['image'] ?? null;

        $this->galleryService->createItem($gallery, $image);
        header('Location: /admin/gallery');
        exit;
    }

    public function update() {
        $gallery = new Gallery();
        $this->mapRequestToObject($gallery, $_POST);
        $image = $_FILES['image'] ?? null;

        $this->galleryService->update($gallery, $image);

        header('Location: /admin/gallery');
        exit;
    }
} 