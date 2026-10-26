<?php
/**
 * Praktikum 5: Implementasi Manajemen Data dan REST API Menggunakan PHP dan MySQL
 * File: index.php
 * Dashboard Dokumentasi REST API dan Penampil Data MySQL via PDO.
 */

require_once __DIR__ . '/config/database.php';

$dbConnected = false;
$errorMessage = '';
$daftar_mahasiswa = [];

try {
    $db = new Database();
    $pdo = $db->getConnection();
    $dbConnected = true;

    $stmt = $pdo->query('SELECT id, npm, nama, program_studi, angkatan, email, created_at FROM mahasiswa ORDER BY id DESC');
    $daftar_mahasiswa = $stmt->fetchAll();
} catch (Exception $e) {
    $errorMessage = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REST API & MySQL - Praktikum 5</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Header Aplikasi -->
    <header class="site-header">
        <div class="container header-brand">
            <a href="index.php" class="brand-logo" title="Beranda SIMA">
                <img src="img/logo-sima.svg" alt="Logo Resmi SIMA Kampus" width="48" height="48">
            </a>
            <div>
                <h1>Sistem Informasi Data Mahasiswa (SIMA)</h1>
                <p>Backend, Basis Data MySQL (PDO), dan REST API Endpoint</p>
            </div>
        </div>
    </header>

    <!-- Navigasi -->
    <nav class="site-nav">
        <div class="container">
            <ul>
                <li><a href="#informasi">Informasi Sistem</a></li>
                <li><a href="#database-view">Data di Basis Data (MySQL)</a></li>
                <li><a href="#rest-api-doc">Dokumentasi REST API</a></li>
                <li><a href="api/mahasiswa.php" target="_blank" rel="noopener noreferrer">Uji Endpoint JSON &nearr;</a></li>
                <li><a href="https://kampus.ac.id" target="_blank" rel="noopener noreferrer">Portal Kampus &UpperRightArrow;</a></li>
            </ul>
        </div>
    </nav>

    <!-- Konten Utama -->
    <main>
        <div class="container">

            <?php if (!$dbConnected): ?>
                <div class="alert-box alert-warning">
                    <strong>Peringatan Koneksi Basis Data:</strong> <?= htmlspecialchars($errorMessage) ?><br>
                    <small>Pastikan layanan MySQL (seperti XAMPP / Laragon / MySQL Service) telah aktif dan skrip <code>database.sql</code> telah dieksekusi.</small>
                </div>
            <?php else: ?>
                <div class="alert-box alert-success">
                    <strong>Koneksi Berhasil:</strong> PHP PDO terhubung secara sukses ke basis data MySQL <code>db_mahasiswa</code>.
                </div>
            <?php endif; ?>

            <!-- 1. Informasi Sistem -->
            <section id="informasi" class="content-section">
                <div class="section-header">
                    <h2>Tentang Praktikum 5</h2>
                    <p>Evolusi penyimpanan dari array session sementara menjadi basis data relasional MySQL dan REST API.</p>
                </div>
                <p>
                    Melanjutkan dari <strong>Praktikum 4</strong>, pada tahap ini aplikasi telah menggunakan 
                    arsitektur modern: data disimpan secara permanen pada basis data <strong>MySQL</strong> dengan 
                    abstraksi <strong>PDO (PHP Data Objects)</strong> untuk mencegah kerentanan SQL Injection. 
                    Selain itu, disediakan antarmuka <strong>REST API</strong> mandiri (<code>/api/mahasiswa.php</code>) 
                    yang menghasilkan respon terstandar <strong>JSON</strong> untuk mendukung integrasi frontend di Praktikum 6.
                </p>

                <div class="info-grid">
                    <article class="info-card">
                        <h3>Status Basis Data</h3>
                        <p class="counter-highlight"><?= $dbConnected ? 'Terhubung (PDO)' : 'Offline' ?></p>
                    </article>
                    <article class="info-card">
                        <h3>Total Record MySQL</h3>
                        <p><?= count($daftar_mahasiswa) ?> Mahasiswa</p>
                    </article>
                    <article class="info-card">
                        <h3>Format Respon API</h3>
                        <p>JSON (RFC 8259)</p>
                    </article>
                </div>
            </section>

            <!-- 2. Data di Basis Data MySQL -->
            <section id="database-view" class="content-section">
                <div class="section-header">
                    <h2>Data Terkini di Tabel `mahasiswa`</h2>
                    <p>Hasil query <code>SELECT * FROM mahasiswa</code> menggunakan PDO Prepared Statements.</p>
                </div>

                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Foto</th>
                                <th>npm</th>
                                <th>Nama Lengkap</th>
                                <th>Program Studi</th>
                                <th>Angkatan</th>
                                <th>Email</th>
                                <th>Dibuat Pada</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($daftar_mahasiswa)): ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 24px; color: var(--text-muted);">
                                        Belum ada data pada tabel <code>mahasiswa</code>.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($daftar_mahasiswa as $mhs): ?>
                                    <tr>
                                        <td>#<?= htmlspecialchars($mhs['id']) ?></td>
                                        <td>
                                            <img src="img/avatar-default.svg" alt="Foto <?= htmlspecialchars($mhs['nama']) ?>" class="avatar-img" width="36" height="36">
                                        </td>
                                        <td><strong><?= htmlspecialchars($mhs['npm']) ?></strong></td>
                                        <td><?= htmlspecialchars($mhs['nama']) ?></td>
                                        <td><span class="badge"><?= htmlspecialchars($mhs['program_studi']) ?></span></td>
                                        <td><?= htmlspecialchars($mhs['angkatan']) ?></td>
                                        <td><a href="mailto:<?= htmlspecialchars($mhs['email']) ?>" class="link-email"><?= htmlspecialchars($mhs['email']) ?></a></td>
                                        <td><small><?= htmlspecialchars($mhs['created_at']) ?></small></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- 3. Dokumentasi REST API -->
            <section id="rest-api-doc" class="content-section">
                <div class="section-header">
                    <h2>Spesifikasi Endpoint REST API Mahasiswa</h2>
                    <p>Endpoint tunggal mandiri: <code>api/mahasiswa.php</code> yang melayani 4 metode HTTP standar.</p>
                </div>

                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Metode</th>
                                <th>Endpoint URL</th>
                                <th>Fungsi</th>
                                <th>Respon Sukses</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="method-badge method-get">GET</span></td>
                                <td><code>api/mahasiswa.php</code></td>
                                <td>Mengambil seluruh data mahasiswa (opsional: <code>?search=kata_kunci</code>)</td>
                                <td>200 OK + JSON Array</td>
                            </tr>
                            <tr>
                                <td><span class="method-badge method-get">GET</span></td>
                                <td><code>api/mahasiswa.php?id={id}</code></td>
                                <td>Mengambil 1 data mahasiswa spesifik berdasarkan ID</td>
                                <td>200 OK / 404 Not Found</td>
                            </tr>
                            <tr>
                                <td><span class="method-badge method-post">POST</span></td>
                                <td><code>api/mahasiswa.php</code></td>
                                <td>Menambahkan mahasiswa baru (Body: JSON atau Form Data)</td>
                                <td>201 Created + Object Baru</td>
                            </tr>
                            <tr>
                                <td><span class="method-badge method-put">PUT</span></td>
                                <td><code>api/mahasiswa.php?id={id}</code></td>
                                <td>Memperbarui data mahasiswa (Body: JSON)</td>
                                <td>200 OK + Object Terupdate</td>
                            </tr>
                            <tr>
                                <td><span class="method-badge method-delete">DELETE</span></td>
                                <td><code>api/mahasiswa.php?id={id}</code></td>
                                <td>Menghapus data mahasiswa dari basis data</td>
                                <td>200 OK</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 24px;">
                    <h3>Contoh Payload JSON (POST / PUT):</h3>
                    <pre class="code-block">{
  "npm": "240101099",
  "nama": "Farhan Alamsyah",
  "program_studi": "Teknik Informatika",
  "angkatan": 2024,
  "email": "farhan.alamsyah@example.com"
}</pre>
                </div>
            </section>

        </div>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> Praktikum Pemrograman Web - Pertemuan 5: PHP, MySQL, & REST API.</p>
            <p>Project Berkelanjutan: Sistem Informasi Data Mahasiswa (Tahap 5/6)</p>
            <p>
                Hubungi Administrator: <a href="mailto:admin.sima@kampus.ac.id">admin.sima@kampus.ac.id</a> | 
                Dokumentasi: <a href="https://github.com" target="_blank" rel="noopener noreferrer">Repository GitHub</a>
            </p>
        </div>
    </footer>

</body>
</html>

