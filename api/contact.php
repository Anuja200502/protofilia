<?php
/**
 * Protofilia Portfolio - Contact API
 * ===================================
 * Handles contact form submissions via AJAX
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/functions.php';

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get form data
$name = sanitize($_POST['name'] ?? '');
$email = sanitize($_POST['email'] ?? '');
$subject = sanitize($_POST['subject'] ?? '');
$message = sanitize($_POST['message'] ?? '');

// Validate
if (empty($name) || empty($email) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

// Rate limiting (simple session-based)
if (isset($_SESSION['last_message_time']) && (time() - $_SESSION['last_message_time']) < 60) {
    echo json_encode(['success' => false, 'message' => 'Please wait a moment before sending another message.']);
    exit;
}

// Save to Supabase
$result = save_message([
    'name' => $name,
    'email' => $email,
    'subject' => $subject,
    'message' => $message,
    'is_read' => false
]);

if ($result['status'] >= 200 && $result['status'] < 300) {
    $_SESSION['last_message_time'] = time();
    echo json_encode(['success' => true, 'message' => 'Thank you! Your message has been sent successfully. I\'ll get back to you soon!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Something went wrong. Please try again later.']);
}