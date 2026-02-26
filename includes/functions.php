<?php
/**
 * Protofilia Portfolio - Helper Functions
 * =======================================
 */

require_once __DIR__ . '/config.php';

// ============================================
// 📁 PROJECT FUNCTIONS
// ============================================

/**
 * Get all projects (optionally filter by featured)
 */
function get_projects($featured = false, $limit = null) {
    $endpoint = 'projects?select=*&order=sort_order.asc,created_at.desc';
    
    if ($featured) {
        $endpoint .= '&is_featured=eq.true';
    }
    
    if ($limit) {
        $endpoint .= '&limit=' . intval($limit);
    }
    
    $result = supabase_request($endpoint);
    
    if ($result['status'] === 200) {
        return $result['data'] ?? [];
    }
    return [];
}

/**
 * Get a single project by slug
 */
function get_project_by_slug($slug) {
    $endpoint = 'projects?slug=eq.' . urlencode($slug) . '&limit=1';
    $result = supabase_request($endpoint);
    
    if ($result['status'] === 200 && !empty($result['data'])) {
        return $result['data'][0];
    }
    return null;
}

/**
 * Get a single project by ID
 */
function get_project_by_id($id) {
    $endpoint = 'projects?id=eq.' . urlencode($id) . '&limit=1';
    $result = supabase_request($endpoint, 'GET', null, true);
    
    if ($result['status'] === 200 && !empty($result['data'])) {
        return $result['data'][0];
    }
    return null;
}

/**
 * Create a new project
 */
function create_project($data) {
    return supabase_request('projects', 'POST', $data, true);
}

/**
 * Update a project
 */
function update_project($id, $data) {
    $endpoint = 'projects?id=eq.' . urlencode($id);
    return supabase_request($endpoint, 'PATCH', $data, true);
}

/**
 * Delete a project
 */
function delete_project($id) {
    $endpoint = 'projects?id=eq.' . urlencode($id);
    return supabase_request($endpoint, 'DELETE', null, true);
}

// ============================================
// 📧 CONTACT/MESSAGE FUNCTIONS
// ============================================

/**
 * Save a contact message
 */
function save_message($data) {
    return supabase_request('messages', 'POST', $data, true);
}

/**
 * Get all messages
 */
function get_messages($limit = 50) {
    $endpoint = 'messages?select=*&order=created_at.desc&limit=' . intval($limit);
    $result = supabase_request($endpoint, 'GET', null, true);
    
    if ($result['status'] === 200) {
        return $result['data'] ?? [];
    }
    return [];
}

/**
 * Mark message as read
 */
function mark_message_read($id) {
    $endpoint = 'messages?id=eq.' . urlencode($id);
    return supabase_request($endpoint, 'PATCH', ['is_read' => true], true);
}

/**
 * Delete a message
 */
function delete_message($id) {
    $endpoint = 'messages?id=eq.' . urlencode($id);
    return supabase_request($endpoint, 'DELETE', null, true);
}

/**
 * Count unread messages
 */
function count_unread_messages() {
    $endpoint = 'messages?is_read=eq.false&select=id';
    $result = supabase_request($endpoint, 'GET', null, true);
    
    if ($result['status'] === 200) {
        return count($result['data'] ?? []);
    }
    return 0;
}

// ============================================
// 👤 PROFILE / SETTINGS FUNCTIONS
// ============================================

/**
 * Get site settings
 */
function get_settings() {
    $endpoint = 'settings?select=*&limit=1';
    $result = supabase_request($endpoint, 'GET', null, true);
    
    if ($result['status'] === 200 && !empty($result['data'])) {
        return $result['data'][0];
    }
    return [
        'name' => 'Your Name',
        'title' => 'Full Stack Developer',
        'bio' => 'Welcome to my portfolio!',
        'email' => 'hello@example.com',
        'phone' => '+94 71 1350 958',
        'location' => 'Matara, Sri Lanka 🇱🇰',
        'github' => '#',
        'linkedin' => '#',
        'twitter' => '#'
    ];
}

/**
 * Update settings
 */
function update_settings($id, $data) {
    $endpoint = 'settings?id=eq.' . urlencode($id);
    return supabase_request($endpoint, 'PATCH', $data, true);
}

// ============================================
// 🛠️ UTILITY FUNCTIONS
// ============================================

/**
 * Generate slug from title
 */
function generate_slug($title) {
    $slug = strtolower(trim($title));
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    return trim($slug, '-');
}

/**
 * Format date nicely
 */
function format_date($date) {
    return date('M d, Y', strtotime($date));
}

/**
 * Truncate text
 */
function truncate($text, $length = 150) {
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length) . '...';
}

/**
 * Get project categories as array
 */
function get_categories() {
    $endpoint = 'projects?select=category';
    $result = supabase_request($endpoint);
    
    if ($result['status'] === 200 && !empty($result['data'])) {
        $categories = array_unique(array_column($result['data'], 'category'));
        return array_filter($categories);
    }
    return [];
}

/**
 * Upload project image
 */
function upload_project_image($file) {
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return ['error' => 'No file uploaded'];
    }
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    if (!in_array($file['type'], $allowedTypes)) {
        return ['error' => 'Invalid file type'];
    }
    
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = 'project-' . uniqid() . '.' . $ext;
    $fileContent = file_get_contents($file['tmp_name']);
    
    $result = supabase_upload('portfolio', 'projects/' . $fileName, $fileContent, $file['type']);
    
    if ($result['status'] >= 200 && $result['status'] < 300) {
        return ['url' => supabase_public_url('portfolio', 'projects/' . $fileName)];
    }
    
    return ['error' => 'Upload failed'];
}
