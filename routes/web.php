<?php
use App\Http\Controllers\HomeController;
Router::get('/', [HomeController::class, 'home']);

use App\Http\Controllers\RegisterController;
Router::get('/register', [RegisterController::class, 'register']);
Router::post('/register', [RegisterController::class, 'submit']);

use App\Http\Controllers\LoginController;
Router::get('/login', [LoginController::class, 'login']);
Router::post('/login', [LoginController::class, 'authenticate']);

use App\Http\Controllers\LogoutController;
Router::get('/logout', [LogoutController::class, 'logout']);

use App\Http\AdminControllers\HomeController as AdminHomeController;
Router::get('/admin', [AdminHomeController::class, 'index']);

use App\Http\AdminControllers\UserController as AdminUserController;
Router::get('/admin/users', [AdminUserController::class, 'index']);
Router::post('/admin/users/delete', [AdminUserController::class, 'delete']);

use App\Http\Controllers\BlogController;
Router::get('/blog', [BlogController::class, 'index']);
Router::get('/blog/{id}', [BlogController::class, 'show']);
Router::get('/api/blog/{id}', [BlogController::class, 'getById']);

use App\Http\AdminControllers\BlogController as AdminBlogController;
Router::get('/admin/posts', [AdminBlogController::class, 'index']);
Router::post('/admin/posts/create', [AdminBlogController::class, 'create']);
Router::post('/admin/posts/update', [AdminBlogController::class, 'update']);
Router::post('/admin/posts/delete', [AdminBlogController::class, 'delete']);

use App\Http\Controllers\GalleryController;
Router::get('/gallery', [GalleryController::class, 'index']);
Router::get('/gallery/{id}', [GalleryController::class, 'show']);
Router::get('/api/gallery/{id}', [GalleryController::class, 'getById']);

use App\Http\AdminControllers\GalleryController as AdminGalleryController;
Router::get('/admin/gallery', [AdminGalleryController::class, 'index']);
Router::post('/admin/gallery/create', [AdminGalleryController::class, 'create']);
Router::post('/admin/gallery/update', [AdminGalleryController::class, 'update']);
