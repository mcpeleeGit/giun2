<?php

use App\Http\AdminControllers\BlogController as AdminBlogController;
use App\Http\AdminControllers\BoardController as AdminBoardController;
use App\Http\AdminControllers\GalleryController as AdminGalleryController;
use App\Http\AdminControllers\HomeController as AdminHomeController;
use App\Http\AdminControllers\AccessLogController as AdminAccessLogController;
use App\Http\AdminControllers\UserController as AdminUserController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\MyPageController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\KakaoAuthController;
use App\Http\Controllers\WorkoutRoutineController;

Router::get('/', [HomeController::class, 'home']);

Router::get('/todo', [TodoController::class, 'index']);
Router::post('/todo', [TodoController::class, 'store']);
Router::post('/todo/{id}/update', [TodoController::class, 'update']);
Router::post('/todo/{id}/toggle', [TodoController::class, 'toggle']);
Router::post('/todo/{id}/delete', [TodoController::class, 'delete']);

Router::post('/workout-routines', [WorkoutRoutineController::class, 'save']);
Router::post('/workout-routines/todos', [WorkoutRoutineController::class, 'saveToTodos']);

Router::get('/gallery', [GalleryController::class, 'index']);
Router::post('/gallery', [GalleryController::class, 'store']);
Router::get('/gallery/{id}', [GalleryController::class, 'show']);

Router::get('/blog', [BlogController::class, 'index']);
Router::post('/blog', [BlogController::class, 'store']);
Router::post('/blog/{id}/update', [BlogController::class, 'update']);
Router::post('/blog/{id}/delete', [BlogController::class, 'delete']);
Router::post('/blog/upload-image', [BlogController::class, 'uploadImage']);
Router::get('/blog/{id}', [BlogController::class, 'show']);

Router::get('/board', [BoardController::class, 'index']);
Router::post('/board', [BoardController::class, 'store']);
Router::post('/board/{id}/update', [BoardController::class, 'update']);
Router::post('/board/{id}/delete', [BoardController::class, 'delete']);

Router::get('/mypage', [MyPageController::class, 'index']);
Router::post('/mypage/update', [MyPageController::class, 'update']);
Router::post('/mypage/delete', [MyPageController::class, 'delete']);

Router::get('/register', [RegisterController::class, 'register']);
Router::post('/register', [RegisterController::class, 'submit']);

Router::get('/login', [LoginController::class, 'login']);
Router::post('/login', [LoginController::class, 'authenticate']);

Router::get('/auth/kakao/redirect', [KakaoAuthController::class, 'redirect']);
Router::get('/auth/kakao/callback', [KakaoAuthController::class, 'callback']);

Router::get('/logout', [LogoutController::class, 'logout']);

// 공개 API
Router::get('/api/blog/{id}', [BlogController::class, 'getById']);
Router::get('/api/gallery/{id}', [GalleryController::class, 'getById']);

// 관리자 페이지
Router::get('/admin', [AdminHomeController::class, 'index']);
Router::get('/admin/analytics', [AdminAccessLogController::class, 'index']);

Router::get('/admin/users', [AdminUserController::class, 'index']);
Router::post('/admin/users/delete', [AdminUserController::class, 'delete']);

Router::get('/admin/posts', [AdminBlogController::class, 'index']);
Router::post('/admin/posts/create', [AdminBlogController::class, 'create']);
Router::post('/admin/posts/update', [AdminBlogController::class, 'update']);
Router::post('/admin/posts/delete', [AdminBlogController::class, 'delete']);

Router::get('/admin/gallery', [AdminGalleryController::class, 'index']);
Router::post('/admin/gallery/create', [AdminGalleryController::class, 'create']);
Router::post('/admin/gallery/update', [AdminGalleryController::class, 'update']);
Router::get('/admin/board', [AdminBoardController::class, 'index']);
Router::post('/admin/board/delete', [AdminBoardController::class, 'delete']);
