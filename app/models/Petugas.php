<?php

class Petugas extends Model
{
    public function byKategori(?string $kategori = null): array
    {
        $where = '';
        if (!empty($kategori)) {
            $kategori = $this->escape($kategori);
            $where = "WHERE COALESCE(NULLIF(petugas.kategori_petugas, ''), petugas.divisi) = '$kategori'";
        }

        $query = mysqli_query($this->db, "SELECT petugas.id_petugas, users.nama, COALESCE(NULLIF(petugas.kategori_petugas, ''), petugas.divisi) AS kategori_petugas
            FROM petugas
            JOIN users ON petugas.id_user = users.id_user
            $where
            ORDER BY users.nama ASC");

        $items = [];
        while ($query && $row = mysqli_fetch_assoc($query)) {
            $items[] = $row;
        }

        return $items;
    }

    public function matchesKategori(string $idPetugas, string $namaKategori): bool
    {
        $idPetugas = $this->escape($idPetugas);
        $namaKategori = $this->escape($namaKategori);

        $query = mysqli_query($this->db, "SELECT id_petugas
            FROM petugas
            WHERE id_petugas = '$idPetugas'
              AND COALESCE(NULLIF(kategori_petugas, ''), divisi) = '$namaKategori'
            LIMIT 1");

        return $query && mysqli_num_rows($query) > 0;
    }
}
