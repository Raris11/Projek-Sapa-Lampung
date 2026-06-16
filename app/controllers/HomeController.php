<?php

class HomeController extends Controller
{
    public function index(): void
    {
        $laporan = new Laporan();
        $this->view('home/index', [
            'statistik_layanan' => $laporan->landingStats(),
            'laporan_selesai' => $laporan->completedForPublic(),
        ]);
    }

    public function cekLaporan(): void
    {
        $ticket = trim($_GET['ticket'] ?? '');
        $laporan = null;
        $riwayat = [];

        if ($ticket !== '') {
            $model = new Laporan();
            $laporan = $model->findByTicket($ticket);
            $riwayat = $laporan ? $model->riwayat($laporan['id_laporan']) : [];
        }

        $this->view('home/cek-laporan', [
            'ticket' => $ticket,
            'laporan' => $laporan,
            'riwayat' => $riwayat,
        ]);
    }
}
