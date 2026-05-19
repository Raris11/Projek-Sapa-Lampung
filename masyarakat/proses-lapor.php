<?php
session_start();
include '../config/koneksi.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // CEK DISINI: Jika ada session login masyarakat, gunakan ID user tersebut. Jika tidak, set menjadi NULL
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'masyarakat' && isset($_SESSION['id_user'])) {
        $id_pelapor = "'" . mysqli_real_escape_string($conn, $_SESSION['id_user']) . "'";
    } else {
        $id_pelapor = "NULL"; 
    }

    $judul      = mysqli_real_escape_string($conn, $_POST['judul_laporan']);
    $id_kategori= mysqli_real_escape_string($conn, $_POST['id_kategori']);
    $deskripsi  = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $alamat     = mysqli_real_escape_string($conn, $_POST['alamat_kejadian']);
    $urgensi    = mysqli_real_escape_string($conn, $_POST['urgensi']);

    // 1. Validasi Keamanan Sisi Server (Wajib Terisi Semua)
    if (empty($judul) || empty($id_kategori) || empty($deskripsi) || empty($alamat) || empty($urgensi) || empty($_FILES['foto_bukti']['name'])) {
        echo "<script>alert('Error: Kolom bertanda wajib diisi tidak boleh kosong!'); window.history.back();</script>";
        exit;
    }

    // 2. Validasi File Upload Gambar
    $filename    = $_FILES['foto_bukti']['name'];
    $filesize    = $_FILES['foto_bukti']['size'];
    $filetmp     = $_FILES['foto_bukti']['tmp_name'];
    $ext         = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png'];

    if (!in_array($ext, $allowed_ext)) {
        echo "<script>alert('Gagal! Format file salah. Sistem hanya menerima file gambar (JPG, JPEG, PNG).'); window.history.back();</script>";
        exit;
    }

    if ($filesize > 5 * 1024 * 1024) { 
        echo "<script>alert('Gagal! Ukuran gambar terlalu besar. Batas maksimal adalah 5MB.'); window.history.back();</script>";
        exit;
    }

    $target_dir = "../assets/img/laporan/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $new_filename = "IMG-" . time() . "." . $ext;
    $target_file  = $target_dir . $new_filename;

    if (move_uploaded_file($filetmp, $target_file)) {
        
        // 3. Auto-generate ID Laporan Unik (Format: RPT-Tahun-Urutan)
        $tahun = date('Y');
        $query_id = mysqli_query($conn, "SELECT id_laporan FROM laporan WHERE id_laporan LIKE 'RPT-$tahun-%' ORDER BY id_laporan DESC LIMIT 1");
        $data_id = mysqli_fetch_assoc($query_id);
        
        if ($data_id) {
            $no_urut = substr($data_id['id_laporan'], -4);
            $no_urut = (int)$no_urut + 1;
            $id_baru = "RPT-" . $tahun . "-" . str_pad($no_urut, 4, "0", STR_PAD_LEFT);
        } else {
            $id_baru = "RPT-" . $tahun . "-0001";
        }

        // 4. Injeksi Data ke Database Relasional ($id_pelapor dimasukkan tanpa tanda petik bungkus tambahan karena sudah dihandle di atas)
        $query_insert = "INSERT INTO laporan (id_laporan, id_pelapor, id_kategori, judul_laporan, deskripsi, alamat_kejadian, foto_bukti, urgensi, status) 
                         VALUES ('$id_baru', $id_pelapor, '$id_kategori', '$judul', '$deskripsi', '$alamat', '$new_filename', '$urgensi', 'Pending')";
        
        if (mysqli_query($conn, $query_insert)) {
            echo "
            <script>
                alert('Laporan Anda BERHASIL terkirim!\\n\\nID LAPORAN: $id_baru\\n\\nCatat dan simpan ID Laporan di atas untuk meninjau status perkembangan pada menu Riwayat Laporan.');
                window.location.href = 'riwayat.php';
            </script>
            ";
            exit;
        } else {
            echo "<script>alert('Kesalahan Sistem: Gagal menginjeksi data ke database.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Kesalahan Server: Gagal mengunggah gambar bukti kejadian.'); window.history.back();</script>";
    }
}
?>