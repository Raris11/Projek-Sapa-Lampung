<?php

// Auto-detect BASE_URL berdasarkan REQUEST_URI
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__);
}

if (!defined('BASE_URL')) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // Jika mengakses langsung ke /public/index.php, maka BASE_URL adalah /public
    // Jika mengakses ke /sapa-lampung-mvc/public/, maka BASE_URL adalah /sapa-lampung-mvc/public
    $basePath = dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php');
    $basePath = '/' . trim($basePath, '/');
    
    define('BASE_URL', $protocol . '://' . $host . $basePath);
}

define('VIEW_PATH', BASE_PATH . '/app/views');

// Role constants
define('ROLE_ADMIN',      'admin');
define('ROLE_PETUGAS',    'petugas');
define('ROLE_MASYARAKAT', 'masyarakat');
