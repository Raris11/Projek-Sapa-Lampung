<?php

// Auto-detect BASE_URL berdasarkan REQUEST_URI
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

if (!defined('BASE_URL')) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    $basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
    if (str_ends_with($basePath, '/public')) {
        $basePath = substr($basePath, 0, -7);
    }
    $basePath = trim($basePath, '/');
    
    define('BASE_URL', rtrim($protocol . '://' . $host . ($basePath ? '/' . $basePath : ''), '/'));
}

define('VIEW_PATH', BASE_PATH . '/app/views');

// Role constants
define('ROLE_ADMIN',      'admin');
define('ROLE_PETUGAS',    'petugas');
define('ROLE_MASYARAKAT', 'masyarakat');
