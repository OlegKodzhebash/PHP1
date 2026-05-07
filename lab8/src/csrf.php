<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function checkCsrf(): void
{
    $token = $_POST['csrf_token'] ?? '';

    if (!$token || $token !== ($_SESSION['csrf_token'] ?? '')) {
        die('Ошибка CSRF-защиты.');
    }
}