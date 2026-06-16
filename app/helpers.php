<?php

function url(string $path = ''): string
{
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function start_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        $savePath = session_save_path();
        if ($savePath && (!is_dir($savePath) || !is_writable($savePath))) {
            session_save_path(sys_get_temp_dir());
        }

        session_start();
    }
}

function flash_alert(string $message, string $redirectPath): void
{
    echo "<script>alert(" . json_encode($message) . "); window.location.href=" . json_encode(url($redirectPath)) . ";</script>";
    exit;
}
