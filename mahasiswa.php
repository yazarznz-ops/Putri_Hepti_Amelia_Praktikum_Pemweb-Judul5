<?php
/**
 * Praktikum 5 - Percobaan 5-2 s.d. 5-4: REST API Mahasiswa (Full CRUD)
 * File: api/mahasiswa.php
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');

require_once __DIR__ . '/../config/database.php';

const KOLOM = 'id, npm, nama, program_studi, angkatan, email, created_at';

function kirim(int $kode, array $payload): void
{
    http_response_code($kode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function bacaInput(): array
{
    $raw  = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : $_POST;   // fallback ke form data
}

/** Mengembalikan daftar nama field wajib yang kosong. */
function fieldKosong(array $input, array $wajib): array
{
    $kosong = [];
    foreach ($wajib as $f) {
        if (!isset($input[$f]) || !is_scalar($input[$f]) || trim((string)$input[$f]) === '') {
            $kosong[] = $f;
        }
    }
    return $kosong;
}

function ambilSatu(PDO $pdo, int $id): array|false
{
    $stmt = $pdo->prepare('SELECT ' . KOLOM . ' FROM mahasiswa WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

// ---------------------------------------------------------------- GET (5-2)
function handleGet(PDO $pdo, ?int $id): void
{
    if ($id !== null) {
        $mahasiswa = ambilSatu($pdo, $id);
        if ($mahasiswa) {
            kirim(200, ['status' => 'success', 'data' => $mahasiswa]);
        } else {
            kirim(404, ['status' => 'error', 'message' => "Mahasiswa dengan ID {$id} tidak ditemukan."]);
        }
        return;
    }

    $search = isset($_GET['search']) ? trim((string)$_GET['search']) : '';
    if ($search !== '') {
        $stmt = $pdo->prepare('SELECT ' . KOLOM . ' FROM mahasiswa
            WHERE npm LIKE :kw1 OR nama LIKE :kw2 OR program_studi LIKE :kw3 ORDER BY id DESC');
        $kw = "%{$search}%";
        $stmt->execute([':kw1' => $kw, ':kw2' => $kw, ':kw3' => $kw]);
    } else {
        $stmt = $pdo->query('SELECT ' . KOLOM . ' FROM mahasiswa ORDER BY id DESC');
    }

    $data = $stmt->fetchAll();
    kirim(200, ['status' => 'success', 'total' => count($data), 'data' => $data]);
}

// --------------------------------------------------------------- POST (5-3)
function handlePost(PDO $pdo): void
{
    $input  = bacaInput();
    $kosong = fieldKosong($input, ['npm', 'nama', 'program_studi', 'angkatan', 'email']);
    if ($kosong) {
        kirim(400, ['status' => 'error', 'message' => 'Field wajib diisi: ' . implode(', ', $kosong)]);
        return;
    }

    $npm = trim((string)$input['npm']);

    $cek = $pdo->prepare('SELECT COUNT(*) FROM mahasiswa WHERE npm = :npm');
    $cek->execute([':npm' => $npm]);
    if ((int)$cek->fetchColumn() > 0) {
        kirim(400, ['status' => 'error', 'message' => "NPM {$npm} sudah terdaftar."]);
        return;
    }

    $stmt = $pdo->prepare('INSERT INTO mahasiswa (npm, nama, program_studi, angkatan, email)
                           VALUES (:npm, :nama, :prodi, :angkatan, :email)');
    $stmt->execute([
        ':npm'      => $npm,
        ':nama'     => trim((string)$input['nama']),
        ':prodi'    => trim((string)$input['program_studi']),
        ':angkatan' => (int)$input['angkatan'],
        ':email'    => trim((string)$input['email']),
    ]);

    $idBaru = (int)$pdo->lastInsertId();
    kirim(201, [
        'status'  => 'success',
        'message' => 'Data mahasiswa berhasil ditambahkan.',
        'data'    => ambilSatu($pdo, $idBaru),
    ]);
}

// ---------------------------------------------------------------- PUT (5-4)
function handlePut(PDO $pdo, ?int $id): void
{
    if (!$id) {
        kirim(400, ['status' => 'error', 'message' => 'ID mahasiswa wajib disertakan.']);
        return;
    }
    if (!ambilSatu($pdo, $id)) {
        kirim(404, ['status' => 'error', 'message' => "Mahasiswa dengan ID {$id} tidak ditemukan."]);
        return;
    }

    $input  = json_decode(file_get_contents('php://input'), true) ?? [];
    $kosong = fieldKosong($input, ['nama', 'program_studi', 'angkatan', 'email']);
    if ($kosong) {
        kirim(400, ['status' => 'error', 'message' => 'Field wajib diisi: ' . implode(', ', $kosong)]);
        return;
    }

    $stmt = $pdo->prepare('UPDATE mahasiswa SET nama = :nama, program_studi = :prodi,
                           angkatan = :angkatan, email = :email WHERE id = :id');
    $stmt->execute([
        ':nama'     => trim((string)$input['nama']),
        ':prodi'    => trim((string)$input['program_studi']),
        ':angkatan' => (int)$input['angkatan'],
        ':email'    => trim((string)$input['email']),
        ':id'       => $id,
    ]);

    kirim(200, [
        'status'  => 'success',
        'message' => 'Data mahasiswa berhasil diperbarui.',
        'data'    => ambilSatu($pdo, $id),
    ]);
}

// ------------------------------------------------------------- DELETE (5-4)
function handleDelete(PDO $pdo, ?int $id): void
{
    if (!$id) {
        kirim(400, ['status' => 'error', 'message' => 'ID mahasiswa wajib disertakan.']);
        return;
    }
    if (!ambilSatu($pdo, $id)) {
        kirim(404, ['status' => 'error', 'message' => "Mahasiswa dengan ID {$id} tidak ditemukan."]);
        return;
    }

    $stmt = $pdo->prepare('DELETE FROM mahasiswa WHERE id = :id');
    $stmt->execute([':id' => $id]);

    kirim(200, ['status' => 'success', 'message' => 'Data mahasiswa berhasil dihapus.']);
}

// ------------------------------------------------------------------ Router
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {      // preflight CORS
    http_response_code(204);
    exit;
}

$id = (isset($_GET['id']) && $_GET['id'] !== '') ? (int)$_GET['id'] : null;

try {
    $pdo = (new Database())->getConnection();

    switch ($method) {
        case 'GET':    handleGet($pdo, $id);    break;
        case 'POST':   handlePost($pdo);        break;
        case 'PUT':    handlePut($pdo, $id);    break;
        case 'DELETE': handleDelete($pdo, $id); break;
        default:
            kirim(405, ['status' => 'error', 'message' => 'Metode HTTP tidak didukung.']);
    }
} catch (Throwable $e) {
    error_log($e->getMessage());   // detail hanya di log server
    kirim(500, ['status' => 'error', 'message' => 'Terjadi kesalahan pada server.']);
}