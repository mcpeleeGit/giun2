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
        // POST 데이터와 파일 가져오기
        $gallery = new Gallery();
        $this->mapRequestToObject($gallery, $_POST);

        $image = $_FILES['image'] ?? null;

        if ($image['error'] !== UPLOAD_ERR_OK) {
            switch ($image['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    echo "<h2>파일이 너무 큽니다.</h2>";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    echo "<h2>파일이 부분적으로만 업로드되었습니다.</h2>";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    echo "<h2>파일이 업로드되지 않았습니다.</h2>";
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    echo "<h2>임시 폴더가 없습니다.</h2>";
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    echo "<h2>디스크에 파일을 쓸 수 없습니다.</h2>";
                    break;
                case UPLOAD_ERR_EXTENSION:
                    echo "<h2>확장에 의해 파일 업로드가 중지되었습니다.</h2>";
                    break;
                default:
                    echo "<h2>알 수 없는 오류가 발생했습니다.</h2>";
                    break;
            }
            exit;
        }

        if ($gallery->title && $gallery->author && $image) {
            // 이미지 업로드 처리
            $uploadDir = '/assets/uploads/gallery/';
            $uploadPath = $_SERVER['DOCUMENT_ROOT'] . $uploadDir;

            // 디렉토리가 존재하지 않으면 생성
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            if (!is_writable($uploadPath)) {
                echo "❌ 업로드 경로에 쓰기 권한이 없습니다.";
            }

            // 파일명 설정: id + 저장일시
            $timestamp = time();
            $fileExtension = pathinfo($image['name'], PATHINFO_EXTENSION);
            $fileName = $gallery->id . '_' . $timestamp . '.' . $fileExtension;
            $filePath = $uploadPath . $fileName;

            if (move_uploaded_file($image['tmp_name'], $filePath)) {
                $gallery->image_path = $uploadDir . $fileName;

                // 갤러리 항목 생성
                $this->galleryService->createItem($gallery);

                // 갤러리 페이지로 리다이렉트
                header('Location: /admin/gallery');
                exit;
            } else {
                echo "<h2>이미지 업로드에 실패했습니다.</h2>";
            }
        } else {
            echo "<h2>모든 필드를 입력해야 합니다.</h2>";
        }
    }
} 