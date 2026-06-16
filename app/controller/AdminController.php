<?php

class AdminController extends Controller
{
    public function updateLaporan(): void
    {
        $this->requireRole(ROLE_ADMIN);

        $laporan = new Laporan();
        $idLaporan = $_POST['id_laporan'] ?? '';
        $idPetugas = $_POST['id_petugas'] ?? null;
        $status = $_POST['status'] ?? 'Menunggu Verifikasi';

        if (!empty($idPetugas)) {
            $kategoriLaporan = $laporan->kategoriName($idLaporan);
            if (!$kategoriLaporan || !(new Petugas())->matchesKategori($idPetugas, $kategoriLaporan)) {
                flash_alert('Petugas tidak sesuai dengan kategori laporan.', '/admin/laporan');
            }

            if (in_array($status, ['Menunggu Verifikasi', 'Diverifikasi'], true)) {
                $status = 'Ditugaskan';
            }
        }

        $ok = $laporan->updateAssignment($idLaporan, $status, $idPetugas);

        flash_alert($ok ? 'Laporan berhasil diperbarui!' : 'Gagal memperbarui data laporan.', '/admin/laporan');
    }

    public function petugasByKategori(): void
    {
        $this->requireRole(ROLE_ADMIN);

        header('Content-Type: application/json');
        echo json_encode((new Petugas())->byKategori($_GET['kategori'] ?? null));
    }
}

