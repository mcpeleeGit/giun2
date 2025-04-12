<?php

namespace App\Http\Controllers;

class LogoutController {
    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        header("Location: /");
        exit;
    }
}
