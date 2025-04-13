<?php
namespace App\Http\Controllers;

class HomeController {
    public function home() { 
        return view('home'); 
    }
    public function blog() { return view('blog'); }
    public function gallery() { return view('gallery'); }
    public function register() { return view('register'); }
    public function login() { return view('login'); }
}
