<?php
/**
 * Protofilia Portfolio - Supabase Configuration
 * =============================================
 * Supabase REST API integration for PHP
 * Supports both local (XAMPP) and Vercel deployment
 */

session_start();

// ============================================
// 🔧 LOCAL OVERRIDES (for XAMPP development)
// ============================================
// If a local config exists, load it for development credentials
if (file_exists(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
}

// ============================================
// 🔧 SUPABASE CREDENTIALS
// ============================================
// Uses environment variables (Vercel) with fallback to local constants
if (!defined('SUPABASE_URL')) {
    define('SUPABASE_URL', getenv('SUPABASE_URL') ?: '');
}
if (!defined('SUPABASE_ANON_KEY')) {
    define('SUPABASE_ANON_KEY', getenv('SUPABASE_ANON_KEY') ?: '');
}
if (!defined('SUPABASE_SERVICE_KEY')) {
    define('SUPABASE_SERVICE_KEY', getenv('SUPABASE_SERVICE_KEY') ?: '');
}

// Site Configuration - Auto-detect URL for Vercel vs local
$isVercel = getenv('VERCEL') || getenv('VERCEL_URL');
if ($isVercel) {
    $protocol = 'https';
    $host = getenv('VERCEL_URL') ?: $_SERVER['HTTP_HOST'];
    $siteUrl = $protocol . '://' . $host;
} else {
    $siteUrl = 'http://localhost/protofilia';
}
if (!defined('SITE_NAME')) {
    define('SITE_NAME', getenv('SITE_NAME') ?: 'Anuja Kodikara');
}
define('SITE_URL', $siteUrl);
if (!defined('SITE_DESCRIPTION')) {
    define('SITE_DESCRIPTION', getenv('SITE_DESCRIPTION') ?: 'Creative Portfolio & Digital Showcase');
}

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