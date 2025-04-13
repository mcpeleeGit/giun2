<?php

namespace App\Http\Controllers;

use App\Services\GalleryService;
use App\Models\Gallery;

class GalleryController {
    private $galleryService;

    public function __construct() {
        $this->galleryService = new GalleryService();
    }

    public function index() {
        // 갤러리 항목 목록 가져오기
        $galleryItems = $this->galleryService->getAll();

        // 갤러리 항목 목록을 뷰에 전달
        view('gallery/index', ['galleryItems' => $galleryItems]);
    }

    public function show($id) {
        $galleryItem = $this->galleryService->getById($id);
        return view('gallery/detail', ['galleryItem' => $galleryItem]);
    }

    public function getById($id) {
        $galleryItem = $this->galleryService->getById($id);
        echo json_encode($galleryItem);
    }
}
