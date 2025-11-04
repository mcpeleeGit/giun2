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
        try {
            error_log("GalleryController::index() - START");
            echo "<!-- GalleryController::index() - START -->\n";
            
            error_log("GalleryController::index() - galleryService type: " . get_class($this->galleryService));
            echo "<!-- GalleryController::index() - galleryService type: " . get_class($this->galleryService) . " -->\n";
            
            error_log("GalleryController::index() - Calling getAll()");
            echo "<!-- GalleryController::index() - Calling getAll() -->\n";
            
            $galleryItems = $this->galleryService->getAll();
            error_log("GalleryController::index() - galleryItems count: " . count($galleryItems));
            echo "<!-- GalleryController::index() - galleryItems count: " . count($galleryItems) . " -->\n";
            
            error_log("GalleryController::index() - Getting current_user()");
            echo "<!-- GalleryController::index() - Getting current_user() -->\n";
            $currentUser = current_user();
            
            error_log("GalleryController::index() - Calling view('gallery/index', ...)");
            echo "<!-- GalleryController::index() - Calling view('gallery/index', ...) -->\n";
            
            view('gallery/index', [
                'galleryItems' => $galleryItems,
                'currentUser' => $currentUser,
                'message' => flash('gallery_message'),
                'error' => flash('gallery_error'),
            ]);
            
            error_log("GalleryController::index() - END");
            echo "<!-- GalleryController::index() - END -->\n";
        } catch (\Exception $e) {
            error_log("GalleryController::index() - EXCEPTION: " . $e->getMessage());
            error_log("GalleryController::index() - EXCEPTION trace: " . $e->getTraceAsString());
            echo "<!-- GalleryController::index() - EXCEPTION: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . " -->\n";
            http_response_code(500);
            throw $e;
        } catch (\Error $e) {
            error_log("GalleryController::index() - ERROR: " . $e->getMessage());
            error_log("GalleryController::index() - ERROR trace: " . $e->getTraceAsString());
            echo "<!-- GalleryController::index() - ERROR: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . " -->\n";
            http_response_code(500);
            throw $e;
        }
    }

    public function store() {
        $user = require_login();
        require_csrf_token($_POST['csrf_token'] ?? null);

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $image = $_FILES['image'] ?? null;

        if ($title === '' || $image === null || ($image['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            flash('gallery_error', '제목과 이미지를 모두 입력해 주세요.');
            redirect('/gallery');
        }

        $gallery = new Gallery();
        $gallery->title = $title;
        $gallery->description = $description;
        $gallery->author = $user->name ?? '익명';

        if (!$this->galleryService->createItem($gallery, $image)) {
            flash('gallery_error', '이미지를 업로드하는 중 오류가 발생했습니다. 잠시 후 다시 시도해 주세요.');
            redirect('/gallery');
        }

        flash('gallery_message', '새로운 갤러리 이미지가 등록되었습니다!');
        redirect('/gallery');
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
