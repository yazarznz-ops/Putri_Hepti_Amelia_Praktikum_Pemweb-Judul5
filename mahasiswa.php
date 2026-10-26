<?php
/**
 * Praktikum 5: Endpoint REST API Mahasiswa Berbasis MySQL & PDO
 * File: api/mahasiswa.php
 * Mendukung metode HTTP GET, POST, PUT, DELETE dengan respon format JSON.
 */

// Header Respons API
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

// Tangani HTTP OPTIONS Pre-flight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/database.php';

// Inisialisasi Koneksi Database
try {
    $db = new Database();
    $pdo = $db->getConnection();
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Gagal terhubung ke database: ' . $e->getMessage(),
        'data' => null
    ]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Routing Berdasarkan HTTP Method
switch ($method) {
    case 'GET':
        handleGet($pdo, $id);
        break;

    case 'POST':
        handlePost($pdo);
        break;

    case 'PUT':
        handlePut($pdo, $id);
        break;

    case 'DELETE':
        handleDelete($pdo, $id);
        break;

    default:
        http_response_code(405);
        echo json_encode([
            'status' => 'error',
            'message' => "Metode HTTP {$method} tidak diizinkan.",
            'data' => null
        ]);
        break;
}

/**
 * 1. Menangani HTTP GET (Ambil Semua Data atau Berdasarkan ID)
 */
function handleGet(PDO $pdo, ?int $id): void {
    if ($id !== null && $id > 0) {
        //Percobaan 5-2 Langkah 2 jika id diberikan

    } else {
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        if (!empty($search)) {
            //Percobaan 5-2 Langkah 2 jika search ada
        } else {
            //Percobaan 5-2 Langkah 2 jika tidak ada search
        }

        $result = $stmt->fetchAll();
        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'total' => count($result),
            'data' => $result
        ]);
    }
}

/**
 * 2. Menangani HTTP POST (Tambah Data Mahasiswa Baru)
 */
function handlePost(PDO $pdo): void {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input)) {
        $input = $_POST;
    }

    $npm = trim($input['npm'] ?? '');
    $nama = trim($input['nama'] ?? '');
    $program_studi = trim($input['program_studi'] ?? '');
    $angkatan = (int)($input['angkatan'] ?? 0);
    $email = trim($input['email'] ?? '');

    if (empty($npm) || empty($nama) || empty($program_studi) || empty($angkatan) || empty($email)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Seluruh bidang isian wajib diisi.']);
        return;
    }

    try {
        $stmt = $pdo->prepare('INSERT INTO mahasiswa (npm, nama, program_studi, angkatan, email) VALUES (:npm, :nama, :prodi, :angkatan, :email)');
        $stmt->execute([
            ':npm' => $npm,
            ':nama' => $nama,
            ':prodi' => $program_studi,
            ':angkatan' => $angkatan,
            ':email' => $email
        ]);

        $newId = (int)$pdo->lastInsertId();
        http_response_code(201);
        echo json_encode([
            'status' => 'success',
            'message' => 'Data mahasiswa berhasil ditambahkan.',
            'data' => [
                'id' => $newId,
                'npm' => $npm,
                'nama' => $nama,
                'program_studi' => $program_studi,
                'angkatan' => $angkatan,
                'email' => $email
            ]
        ]);
    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan data: ' . $e->getMessage()]);
    }
}

/**
 * 3. Menangani HTTP PUT (Perbarui Data Mahasiswa)
 */
function handlePut(PDO $pdo, ?int $id): void {
   
}

/**
 * 4. Menangani HTTP DELETE (Hapus Data Mahasiswa)
 */
function handleDelete(PDO $pdo, ?int $id): void {
    
}
