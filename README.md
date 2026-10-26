# Petunjuk Praktikum 5 — Percobaan 4: Implementasi Endpoint HTTP PUT & DELETE (Full CRUD RESTful API)

## 1. Tujuan Pembelajaran
Fokus pada penyempurnaan REST API lengkap dengan penanganan HTTP PUT untuk pembaruan data dan DELETE untuk penghapusan record.

---

## 2. File Kerja
- Basis data: `database.sql`
- Konfigurasi PDO: `config/database.php`
- REST API Endpoint: `api/mahasiswa.php`
- HTTP Client Test: `test_api.http`
- Dashboard API & MySQL View: `index.php`

---

## 3. Cara Menjalankan Server PHP
Buka terminal pada direktori ini dan jalankan:
```bash
php -S localhost:8000
```
Buka peramban pada `http://localhost:8000` atau gunakan berkas `test_api.http` untuk pengujian endpoint API.
