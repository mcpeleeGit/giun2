<?php

namespace App\Http\AdminControllers;
use App\Http\AdminControllers\Common\Controller;
class HomeController extends Controller {
    public function index() {
        // 관리자 대시보드 페이지 렌더링
        adminView('index');
    }
}
