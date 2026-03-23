<?php
/**
 * TinyMCE Image Upload Handler
 * Receives image uploads from the TinyMCE editor and returns the URL
 */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => 'No se recibió el archivo.']);
    exit;
}

$file = $_FILES['file'];
$allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
$allowedExts  = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
$maxSize      = 5 * 1024 * 1024; // 5MB

$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowedExts)) {
    http_response_code(400);
    echo json_encode(['error' => 'Extensión no permitida.']);
    exit;
}

if ($file['size'] > $maxSize) {
    http_response_code(400);
    echo json_encode(['error' => 'Archivo demasiado grande. Máximo 5MB.']);
    exit;
}

// Verify MIME via magic bytes
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mimeType, $allowedTypes)) {
    http_response_code(400);
    echo json_encode(['error' => 'Tipo de archivo no permitido.']);
    exit;
}

$newName = uniqid('content_', true) . '.' . $ext;
$uploadDir = __DIR__ . '/../uploads/blog/';
$destination = $uploadDir . $newName;

if (!move_uploaded_file($file['tmp_name'], $destination)) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al subir el archivo.']);
    exit;
}

// TinyMCE expects { "location": "url" }
echo json_encode(['location' => '../uploads/blog/' . $newName]);
