<?php

namespace App\Http\AdminControllers;

use App\Http\AdminControllers\Common\Controller;
use App\Models\Gallery;
use App\Services\GalleryService;

class GalleryController extends Controller
{
    private GalleryService $galleryService;

    public function __construct()
    {
        parent::__construct();
        $this->galleryService = new GalleryService();
    }

    public function index(): void
    {
        $galleryItems = $this->galleryService->getAll();

        adminView('gallery', [
            'galleryItems' => $galleryItems,
            'admin' => $this->adminUser,
        ]);
    }

    public function create(): void
    {
        $this->ensurePostWithCsrf();

        $title = $this->getPostString('title');
        $description = $this->getPostString('description', true) ?? '';
        $author = $this->getPostString('author');
        $image = $_FILES['image'] ?? null;

        if ($title === null || $author === null || $image === null) {
            $this->redirectWithError('/admin/gallery', '필수 항목을 모두 입력해 주세요.');
        }

        $gallery = new Gallery();
        $gallery->title = $title;
        $gallery->description = $description;
        $gallery->author = $author;

        if (!$this->galleryService->createItem($gallery, $image)) {
            $this->redirectWithError('/admin/gallery', '갤러리 항목 등록에 실패했습니다.');
        }

        $this->redirectWithSuccess('/admin/gallery', '갤러리 항목이 등록되었습니다.');
    }

    public function update(): void
    {
        $this->ensurePostWithCsrf();

        $id = $this->getPostInt('id');
        $title = $this->getPostString('title');
        $description = $this->getPostString('description', true) ?? '';
        $author = $this->getPostString('author');
        $image = $_FILES['image'] ?? null;

        if ($id === null || $title === null || $author === null) {
            $this->redirectWithError('/admin/gallery', '입력값을 다시 확인해 주세요.');
        }

        $gallery = new Gallery();
        $gallery->id = $id;
        $gallery->title = $title;
        $gallery->description = $description;
        $gallery->author = $author;

        if (!$this->galleryService->update($gallery, $image)) {
            $this->redirectWithError('/admin/gallery', '갤러리 항목 수정에 실패했습니다.');
        }

        $this->redirectWithSuccess('/admin/gallery', '갤러리 항목이 수정되었습니다.');
    }
}
