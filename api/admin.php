<?php
/**
 * Vercel Serverless Router - Admin Pages
 * Routes all admin page requests to the correct PHP file
 */

$page = $_GET['page'] ?? 'login';
$basePath = dirname(__DIR__);

// Map page parameter to actual PHP file
$pageMap = [
    'login' => $basePath . '/admin/login.php',
    'dashboard' => $basePath . '/admin/dashboard.php',
    'manage-projects' => $basePath . '/admin/manage-projects.php',
    'settings' => $basePath . '/admin/settings.php',
];

// Validate and serve the requested page
if (isset($pageMap[$page]) && file_exists($pageMap[$page])) {
    require $pageMap[$page];
} else {
    // Default to login
    require $basePath . '/admin/login.php';
}
