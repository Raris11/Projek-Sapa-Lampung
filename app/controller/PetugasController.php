<?php

class PetugasController extends Controller
{
    public function updateProgress(): void
    {
        $this->requireRole(ROLE_PETUGAS);

        if (empty($_FILES['foto_progres']['name'])) {
            flash_alert('Gagal! Foto bukti pekerjaan wajib diunggah.', '/petugas/dashboard');
        }

        $ext = strtolower(pathinfo($_FILES['foto_progres']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
            flash_alert('Gagal! Format berkas harus gambar JPG/JPEG/PNG.', '/petugas/dashboard');
        }

        $targetDir = BASE_PATH . '/public/assets/img/laporan/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $newFilename = 'PRG-' . time() . '.' . $ext;
        if (!move_uploaded_file($_FILES['foto_progres']['tmp_name'], $targetDir . $newFilename)) {
            flash_alert('Terjadi kesalahan saat mengunggah foto progres.', '/petugas/dashboard');
        }

        [$persentase, $statusTracking] = $this->trackingMeta($_POST['status'] ?? '');

        $laporan = new Laporan();
        $updated = $laporan->updateProgress($_POST['id_laporan'] ?? '', $_POST['status'] ?? '');
        $tracked = $updated && $laporan->addTracking($_POST['id_laporan'] ?? '', $statusTracking, $_POST['keterangan'] ?? '', $newFilename, $persentase);

        flash_alert($tracked ? 'Progres laporan berhasil diperbarui!' : 'Terjadi kesalahan saat memproses data lapangan.', '/petugas/dashboard');
    }

    private function trackingMeta(string $status): array
    {
        return match ($status) {
            'Diproses' => [75, 'Pengerjaan Lapangan'],
            'Selesai' => [100, 'Selesai'],
            default => [25, 'Tugas Diterima'],
        };
    }
}
