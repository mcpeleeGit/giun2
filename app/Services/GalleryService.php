<?php

namespace App\Services;

use App\Repositories\GalleryRepository;

class GalleryService {
    protected $galleryRepository;

    public function __construct() {
        $this->galleryRepository = new GalleryRepository();
    }

    public function getAll() {
        return $this->galleryRepository->getAll();
    }

    public function createItem($gallery) {
        return $this->galleryRepository->create($gallery);
    }
} 