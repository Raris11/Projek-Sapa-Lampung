<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo json_encode([]);
    exit;
}

$kategori = isset($_GET['kategori']) ? mysqli_real_escape_string($conn, $_GET['kategori']) : '';

if (!empty($kategori)) {
    $query = mysqli_query($conn, "SELECT petugas.id_petugas, users.nama 
                                  FROM petugas 
                                  JOIN users ON petugas.id_user = users.id_user 
                                  WHERE petugas.divisi = '$kategori'");
} else {
    $query = mysqli_query($conn, "SELECT petugas.id_petugas, users.nama 
                                  FROM petugas 
                                  JOIN users ON petugas.id_user = users.id_user");
}

$response = [];
while ($row = mysqli_fetch_assoc($query)) {
    $response[] = $row;
}

header('Content-Type: application/json');
echo json_encode($response);
exit;