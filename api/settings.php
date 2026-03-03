<?php
/**
 * Protofilia Admin - Update Settings API
 */
require_once __DIR__ . '/../includes/functions.php';

if (!is_admin_logged_in()) {
    redirect(SITE_URL . '/admin/login.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(SITE_URL . '/admin/settings.php');
}

// Get current settings to find the ID
$currentSettings = get_settings();
$settingsId = $currentSettings['id'] ?? null;

if (!$settingsId) {
    set_flash('error', 'Settings record not found. Please create one in Supabase first.');
    redirect(SITE_URL . '/admin/settings.php');
}

// Build the update data
$updateData = [
    'name'     => trim($_POST['name'] ?? ''),
    'title'    => trim($_POST['title'] ?? ''),
    'bio'      => trim($_POST['bio'] ?? ''),
    'email'    => trim($_POST['email'] ?? ''),
    'phone'    => trim($_POST['phone'] ?? ''),
    'location' => trim($_POST['location'] ?? ''),
    'linkedin' => trim($_POST['linkedin'] ?? ''),
    'github'   => trim($_POST['github'] ?? ''),
    'twitter'  => trim($_POST['twitter'] ?? ''),
];

// Validate required fields
if (empty($updateData['name'])) {
    set_flash('error', 'Name is required.');
    redirect(SITE_URL . '/admin/settings.php');
}

if (empty($updateData['email'])) {
    set_flash('error', 'Email is required.');
    redirect(SITE_URL . '/admin/settings.php');
}

// Handle avatar upload if provided
if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['avatar'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    
    if (!in_array($file['type'], $allowedTypes)) {
        set_flash('error', 'Invalid file type. Only JPG, PNG, and WebP are allowed.');
        redirect(SITE_URL . '/admin/settings.php');
    }
    
    if ($file['size'] > 2 * 1024 * 1024) {
        set_flash('error', 'File too large. Maximum size is 2MB.');
        redirect(SITE_URL . '/admin/settings.php');
    }
    
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = 'avatar-' . time() . '.' . $ext;
    $fileContent = file_get_contents($file['tmp_name']);
    
    $uploadResult = supabase_upload('portfolio', 'avatars/' . $fileName, $fileContent, $file['type']);
    
    if ($uploadResult['status'] >= 200 && $uploadResult['status'] < 300) {
        $updateData['avatar_url'] = supabase_public_url('portfolio', 'avatars/' . $fileName);
    } else {
        set_flash('error', 'Failed to upload avatar. Please try again.');
        redirect(SITE_URL . '/admin/settings.php');
    }
}

// Update settings in Supabase
$result = update_settings($settingsId, $updateData);

if ($result['status'] >= 200 && $result['status'] < 300) {
    set_flash('success', 'Profile settings updated successfully! ✨');
} else {
    set_flash('error', 'Failed to update settings. Please try again.');
}

redirect(SITE_URL . '/admin/settings.php');
