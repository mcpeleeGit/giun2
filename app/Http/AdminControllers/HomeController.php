<?php

namespace App\Http\AdminControllers;

use App\Http\AdminControllers\Common\Controller;
use App\Services\BlogService;
use App\Services\GalleryService;
use App\Services\UserService;

class HomeController extends Controller
{
    private UserService $userService;
    private BlogService $blogService;
    private GalleryService $galleryService;

    public function __construct()
    {
        parent::__construct();
        $this->userService = new UserService();
        $this->blogService = new BlogService();
        $this->galleryService = new GalleryService();
    }

    public function index(): void
    {
        $users = $this->userService->getAllUsers();
        $posts = $this->blogService->getAll();
        $galleryItems = $this->galleryService->getAll();

        adminView('index', [
            'admin' => $this->adminUser,
            'stats' => [
                'users' => count($users),
                'posts' => count($posts),
                'gallery' => count($galleryItems),
            ],
            'recentUsers' => array_slice($users, 0, 5),
            'recentPosts' => array_slice($posts, 0, 5),
            'recentGallery' => array_slice($galleryItems, 0, 5),
        ]);
    }
}
