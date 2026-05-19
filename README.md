Cara Menjalankan Project SAPA Menggunakan Laragon

**1. Clone Repository GitHub**
Buka terminal/CMD lalu jalankan:
git clone https://github.com/USERNAME/sapa-lampung.git

**2. Pindahkan Folder Project**
Masukkan folder project ke:
C:\laragon\www\
Contoh:
C:\laragon\www\sapa-lampung

**3. Jalankan Laragon**
Buka Laragon lalu klik:
Start All
Pastikan:
Apache aktif
MySQL aktif

**4. Buat Database**
Buka browser:
http://localhost/phpmyadmin
Lalu:
Klik New
Isi nama database:
sapa_lampung
Klik Create

**5. Create Table Database**
Klik database sapa_lampung → pilih menu SQL → lalu jalankan query berikut:

Table Users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100),
    email VARCHAR(100),
    password VARCHAR(255),
    role ENUM('admin','petugas','masyarakat'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

Table Laporan
CREATE TABLE laporan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_laporan VARCHAR(50),
    user_id INT,
    kategori VARCHAR(100),
    judul VARCHAR(255),
    deskripsi TEXT,
    lokasi VARCHAR(255),
    status ENUM('pending','diproses','selesai'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

Table Petugas
CREATE TABLE petugas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100),
    bidang VARCHAR(100),
    no_hp VARCHAR(20),
    status ENUM('aktif','nonaktif')
);

**6. Jalankan Project**
Buka browser:
http://localhost/sapa-lampung
