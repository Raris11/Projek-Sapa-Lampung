<?php

class MasyarakatController extends Controller
{
    public function create(): void
    {
        start_session();

        $kategori = new Kategori();
        $this->view('masyarakat/buat-laporan', [
            'query_kat' => $kategori->all(),
            'is_logged_in' => isset($_SESSION['role']) && $_SESSION['role'] === ROLE_MASYARAKAT,
            'nama_user' => $_SESSION['nama'] ?? 'Tamu / Umum',
        ]);
    }

    public function store(): void
    {
        start_session();

        $required = ['judul_laporan', 'id_kategori', 'deskripsi', 'alamat_kejadian', 'urgensi'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                flash_alert('Error: Kolom bertanda wajib diisi tidak boleh kosong!', '/masyarakat/buat-laporan');
            }
        }

        if (empty($_FILES['foto_bukti']['name'])) {
            flash_alert('Foto bukti wajib diunggah.', '/masyarakat/buat-laporan');
        }

        $filename = $_FILES['foto_bukti']['name'];
        $filesize = $_FILES['foto_bukti']['size'];
        $tmp = $_FILES['foto_bukti']['tmp_name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
            flash_alert('Gagal! Format file salah. Sistem hanya menerima JPG, JPEG, atau PNG.', '/masyarakat/buat-laporan');
        }

        if ($filesize > 5 * 1024 * 1024) {
            flash_alert('Gagal! Ukuran gambar terlalu besar. Batas maksimal adalah 5MB.', '/masyarakat/buat-laporan');
        }

        $targetDir = BASE_PATH . '/public/assets/img/laporan/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $newFilename = 'IMG-' . time() . '.' . $ext;
        if (!move_uploaded_file($tmp, $targetDir . $newFilename)) {
            flash_alert('Kesalahan Server: Gagal mengunggah gambar bukti kejadian.', '/masyarakat/buat-laporan');
        }

        $laporan = new Laporan();
        $idBaru = $laporan->create([
            'id_pelapor' => (isset($_SESSION['role'], $_SESSION['id_user']) && $_SESSION['role'] === ROLE_MASYARAKAT) ? $_SESSION['id_user'] : null,
            'nama_pelapor' => $_POST['nama_pelapor'] ?? null,
            'email_pelapor' => $_POST['email_pelapor'] ?? null,
            'telepon_pelapor' => $_POST['telepon_pelapor'] ?? null,
            'id_kategori' => $_POST['id_kategori'],
            'judul_laporan' => $_POST['judul_laporan'],
            'deskripsi' => $_POST['deskripsi'],
            'alamat_kejadian' => $_POST['alamat_kejadian'],
            'foto_bukti' => $newFilename,
            'urgensi' => $_POST['urgensi'],
        ]);

        if (!$idBaru) {
            flash_alert('Kesalahan Sistem: Gagal menyimpan data laporan.', '/masyarakat/buat-laporan');
        }

        flash_alert("Laporan Anda BERHASIL terkirim!\n\nNOMOR TIKET: $idBaru\n\nCatat nomor tiket untuk cek status laporan.", '/cek-laporan.php?ticket=' . urlencode($idBaru));
    }
}
