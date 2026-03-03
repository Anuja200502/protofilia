<?php
/**
 * Vercel Serverless Router
 * Routes all page requests to the correct PHP file
 */

// Debug: uncomment to see paths
// echo "Base: " . dirname(__DIR__) . "<br>";
// echo "Current: " . __DIR__ . "<br>";

$page = $_GET['page'] ?? 'index';
$basePath = dirname(__DIR__);

// Allowed pages map
$pageMap = [
    'index'                 => $basePath . '/index.php',
    'about'                 => $basePath . '/about.php',
    'contact'               => $basePath . '/contact.php',
    'projects'              => $basePath . '/projects.php',
    'admin/login'           => $basePath . '/admin/login.php',
    'admin/dashboard'       => $basePath . '/admin/dashboard.php',
    'admin/manage-projects' => $basePath . '/admin/manage-projects.php',
    'admin/settings'        => $basePath . '/admin/settings.php',
];

// Sanitize page parameter
$page = preg_replace('/[^a-z0-9\-\/]/', '', $page);

// Serve the requested page
if (isset($pageMap[$page]) && file_exists($pageMap[$page])) {
    require $pageMap[$page];
} else {
    // Show debug info if file not found
    http_response_code(404);
    echo "Page not found: " . htmlspecialchars($page) . "<br>";
    echo "Looking for: " . (isset($pageMap[$page]) ? $pageMap[$page] : 'unknown') . "<br>";
    echo "Base path: " . $basePath . "<br>";
    echo "Files in base: <pre>";
    if (is_dir($basePath)) {
        print_r(scandir($basePath));
    } else {
        echo "Base dir does not exist";
    }
    echo "</pre>";
}
