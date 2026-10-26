<?php
/**
 * Praktikum 5: Implementasi Manajemen Data dan REST API Menggunakan PHP dan MySQL
 * File: config/database.php
 * Konfigurasi koneksi basis data menggunakan PDO (PHP Data Objects).
 */

class Database {
    private string $host = '127.0.0.1';
    private string $db_name = 'db_mahasiswa';
    private string $username = 'root';
    private string $password = '';
    private int $port = 3306;
    private ?PDO $conn = null;

    /**
     * Membuka dan mengembalikan koneksi PDO ke MySQL
     *
     * @return PDO
     * @throws PDOException
     */
    public function getConnection(): PDO {
        if ($this->conn === null) {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

           // Percobaan 5-1 Langkah 4
           
        }

        return $this->conn;
    }
}

