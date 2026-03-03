<?php
/**
 * Protofilia - Avatar Upload API
 * Uploads avatar to Supabase Storage using Service Key (bypasses RLS)
 */
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

// Simple auth: verify a token that only the admin page knows
$authToken = $_GET['token'] ?? '';
$expectedToken = md5(SUPABASE_SERVICE_KEY . 'upload');
if ($authToken !== $expectedToken) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Read the raw body (file data sent as binary)
$fileData = file_get_contents('php://input');
$contentType = $_SERVER['CONTENT_TYPE'] ?? 'image/jpeg';
$fileName = $_GET['filename'] ?? ('avatar-' . time() . '.jpg');

if (empty($fileData)) {
    http_response_code(400);
    echo json_encode(['error' => 'No file data received']);
    exit;
}

// Validate content type
$allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
if (!in_array($contentType, $allowedTypes)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid file type. Only JPG, PNG, and WebP allowed.']);
    exit;
}

// Validate size (2MB max)
if (strlen($fileData) > 2 * 1024 * 1024) {
    http_response_code(400);
    echo json_encode(['error' => 'File too large. Max 2MB.']);
    exit;
}

// Upload to Supabase Storage using SERVICE KEY (bypasses RLS)
$filePath = 'avatars/' . $fileName;
$uploadUrl = SUPABASE_URL . '/storage/v1/object/portfolio/' . $filePath;

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $uploadUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $fileData,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . SUPABASE_SERVICE_KEY,
        'apikey: ' . SUPABASE_SERVICE_KEY,
        'Content-Type: ' . $contentType,
        'x-upsert: true'
    ]
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode >= 200 && $httpCode < 300) {
    $publicUrl = SUPABASE_URL . '/storage/v1/object/public/portfolio/' . $filePath;
    echo json_encode([
        'success' => true,
        'url' => $publicUrl
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'error' => 'Upload failed',
        'details' => $response,
        'status' => $httpCode
    ]);
}
