<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\MyPageController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;

Router::get('/', [HomeController::class, 'home']);

Router::get('/todo', [TodoController::class, 'index']);
Router::post('/todo', [TodoController::class, 'store']);
Router::post('/todo/{id}/toggle', [TodoController::class, 'toggle']);
Router::post('/todo/{id}/delete', [TodoController::class, 'delete']);

Router::get('/board', [BoardController::class, 'index']);
Router::post('/board', [BoardController::class, 'store']);

Router::get('/mypage', [MyPageController::class, 'index']);

Router::get('/register', [RegisterController::class, 'register']);
Router::post('/register', [RegisterController::class, 'submit']);

Router::get('/login', [LoginController::class, 'login']);
Router::post('/login', [LoginController::class, 'authenticate']);

Router::get('/logout', [LogoutController::class, 'logout']);
