<?php

class Database {
    private static $instance = null;
    private $conn;

    private $host     = 'localhost';
    private $user     = 'root';
    private $password = '';
    private $dbname   = 'sapa_lampung';

    private function __construct() {
        $this->conn = mysqli_connect($this->host, $this->user, $this->password, $this->dbname);
        if (!$this->conn) {
            die("Koneksi database gagal: " . mysqli_connect_error());
        }
        mysqli_set_charset($this->conn, 'utf8mb4');
    }

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): mysqli {
        return $this->conn;
    }
}
