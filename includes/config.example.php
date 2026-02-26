<?php
/**
 * Protofilia Portfolio - Supabase Configuration
 * =============================================
 * Copy this file to config.php and fill in your Supabase credentials
 */

session_start();

// ============================================
// 🔧 SUPABASE CREDENTIALS - UPDATE THESE!
// ============================================
define('SUPABASE_URL', 'https://YOUR_PROJECT_REF.supabase.co');
define('SUPABASE_ANON_KEY', 'your-anon-key-here');
define('SUPABASE_SERVICE_KEY', 'your-service-role-key-here'); // For admin operations

// Site Configuration
define('SITE_NAME', 'Your Name');
define('SITE_URL', 'http://localhost/protofilia');
define('SITE_DESCRIPTION', 'Creative Portfolio & Digital Showcase');

// ============================================
// 📡 Supabase REST API Helper Functions
// ============================================

/**
 * Make a request to Supabase REST API
 */
function supabase_request($endpoint, $method = 'GET', $data = null, $useServiceKey = false) {
    $url = SUPABASE_URL . '/rest/v1/' . $endpoint;
    $key = $useServiceKey ? SUPABASE_SERVICE_KEY : SUPABASE_ANON_KEY;
    
    $headers = [
        'apikey: ' . $key,
        'Authorization: Bearer ' . $key,
        'Content-Type: application/json',
        'Prefer: return=representation'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    switch (strtoupper($method)) {
        case 'POST':
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            break;
        case 'PATCH':
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            break;
        case 'DELETE':
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            break;
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return ['error' => $error, 'status' => 0];
    }
    
    return [
        'data' => json_decode($response, true),
        'status' => $httpCode
    ];
}

/**
 * Supabase Auth - Sign In
 */
function supabase_sign_in($email, $password) {
    $url = SUPABASE_URL . '/auth/v1/token?grant_type=password';
    
    $headers = [
        'apikey: ' . SUPABASE_ANON_KEY,
        'Content-Type: application/json'
    ];
    
    $data = json_encode([
        'email' => $email,
        'password' => $password
    ]);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return ['data' => ['message' => 'cURL Error: ' . $error], 'status' => 0];
    }
    
    return [
        'data' => json_decode($response, true),
        'status' => $httpCode
    ];
}

/**
 * Upload file to Supabase Storage
 */
function supabase_upload($bucket, $filePath, $fileContent, $contentType) {
    $url = SUPABASE_URL . '/storage/v1/object/' . $bucket . '/' . $filePath;
    
    $headers = [
        'apikey: ' . SUPABASE_SERVICE_KEY,
        'Authorization: Bearer ' . SUPABASE_SERVICE_KEY,
        'Content-Type: ' . $contentType,
        'x-upsert: true'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fileContent);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'data' => json_decode($response, true),
        'status' => $httpCode
    ];
}

/**
 * Get public URL for a file in Supabase Storage
 */
function supabase_public_url($bucket, $filePath) {
    return SUPABASE_URL . '/storage/v1/object/public/' . $bucket . '/' . $filePath;
}

/**
 * Simple helper to check if admin is logged in
 */
function is_admin_logged_in() {
    return isset($_SESSION['admin_token']) && !empty($_SESSION['admin_token']);
}

/**
 * Redirect helper
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Sanitize input
 */
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Flash message helpers
 */
function set_flash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
