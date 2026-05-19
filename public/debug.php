<?php
/**
 * Debug helper untuk troubleshoot routing
 */

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/config/app.php';

echo "<h1>Debug Info (Updated)</h1>";
echo "<pre>";
echo "BASE_PATH: " . BASE_PATH . "\n";
echo "BASE_URL: " . BASE_URL . "\n";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "\n";
echo "REQUEST_METHOD: " . ($_SERVER['REQUEST_METHOD'] ?? 'N/A') . "\n";
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'N/A') . "\n";
echo "\nRouter URI calculation (UPDATED):\n";

// Simulasi getUri() yang sudah diperbaiki
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$uri = strtok($uri, '?');
$scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php');
$scriptDir = '/' . trim($scriptDir, '/');
if (strpos($uri, $scriptDir) === 0 && $scriptDir !== '/') {
    $uri = substr($uri, strlen($scriptDir));
}
$cleanUri = '/' . ltrim($uri, '/');
echo "Script Directory: " . $scriptDir . "\n";
echo "Clean URI: " . $cleanUri . "\n";

echo "\nExpected route for login: /login\n";
echo "Expected route for home: /\n";

echo "\nmod_rewrite aktif: " . (function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules()) ? "Ya" : "Tidak bisa detect") . "\n";
echo "\n.htaccess di public/:\n";
echo file_get_contents(BASE_PATH . '/public/.htaccess') ?? "File tidak ditemukan";
echo "</pre>";
echo "<hr>";
echo "<p><a href='" . BASE_URL . "/'>Kembali ke home</a></p>";
echo "<p><a href='" . BASE_URL . "/login'>Test ke login</a></p>";
