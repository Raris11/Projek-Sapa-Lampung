<?php
session_start();
include 'config/koneksi.php';

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    
    if (mysqli_num_rows($query) === 1) {
        $row = mysqli_fetch_assoc($query);
        
        if ($password === $row['password'] || password_verify($password, $row['password'])) {
            $_SESSION['id_user'] = $row['id_user'];
            $_SESSION['nama'] = $row['nama'];
            $_SESSION['role'] = $row['role'];

            if ($row['role'] === 'admin') {
                header("Location: admin/dashboard.php");
                exit;
            } elseif ($row['role'] === 'petugas') {
                header("Location: petugas/petugas.php");
                exit;
            } elseif ($row['role'] === 'masyarakat') {
                header("Location: masyarakat/dashboard.php");
                exit;
            }
        } else {
            echo "<script>alert('Password salah!'); window.location.href='login.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Email tidak terdaftar!'); window.location.href='login.php';</script>";
        exit;
    }
} else {
    header("Location: login.php");
    exit;
}
?>