<?php

namespace App\Http\AdminControllers\Common;

use App\Models\User;

class Controller
{
    protected User $adminUser;

    public function __construct()
    {
        $this->adminUser = require_admin();
    }

    protected function requirePost(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            http_response_code(405);
            exit('허용되지 않는 요청입니다.');
        }
    }

    protected function requireCsrfToken(): void
    {
        require_csrf_token($_POST['csrf_token'] ?? null);
    }

    protected function ensurePostWithCsrf(): void
    {
        $this->requirePost();
        $this->requireCsrfToken();
    }

    protected function getPostString(string $key, bool $allowEmpty = false): ?string
    {
        $value = filter_input(INPUT_POST, $key, FILTER_UNSAFE_RAW);
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        if ($value === '' && !$allowEmpty) {
            return null;
        }

        return $value;
    }

    protected function getPostInt(string $key): ?int
    {
        $value = filter_input(INPUT_POST, $key, FILTER_VALIDATE_INT);

        return $value === false ? null : $value;
    }

    protected function redirectWithSuccess(string $path, string $message): void
    {
        flash('admin_notice', $message);
        redirect($path);
    }

    protected function redirectWithError(string $path, string $message): void
    {
        flash('admin_error', $message);
        redirect($path);
    }
}
