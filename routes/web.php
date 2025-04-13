<?php
use App\Http\Controllers\HomeController;
Router::get('/', [HomeController::class, 'home']);

Router::get('/gallery', [HomeController::class, 'gallery']);
Router::get('/register', [HomeController::class, 'register']);
Router::get('/login', [HomeController::class, 'login']);

use App\Http\Controllers\RegisterController;
Router::post('/register', [RegisterController::class, 'submit']);

use App\Http\Controllers\LoginController;
Router::post('/login', [LoginController::class, 'authenticate']);

use App\Http\Controllers\LogoutController;
Router::get('/logout', [LogoutController::class, 'logout']);

use App\Http\Controllers\BlogController;
Router::get('/blog', [BlogController::class, 'index']);
Router::get('/blog/{id}', [BlogController::class, 'show']);
Router::get('/api/blog/{id}', [BlogController::class, 'getById']);

use App\Http\AdminControllers\HomeController as AdminHomeController;
Router::get('/admin', [AdminHomeController::class, 'index']);

use App\Http\AdminControllers\UserController as AdminUserController;
Router::get('/admin/users', [AdminUserController::class, 'index']);
Router::post('/admin/users/delete', [AdminUserController::class, 'delete']);

use App\Http\AdminControllers\BlogController as AdminBlogController;
Router::get('/admin/posts', [AdminBlogController::class, 'index']);
Router::post('/admin/posts/create', [AdminBlogController::class, 'create']);
Router::post('/admin/posts/update', [AdminBlogController::class, 'update']);
Router::post('/admin/posts/delete', [AdminBlogController::class, 'delete']);
