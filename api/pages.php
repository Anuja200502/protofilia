<?php
/**
 * Vercel Serverless Router - Public Pages
 * Routes all public page requests to the correct PHP file
 */

$page = $_GET['page'] ?? 'index';
$basePath = dirname(__DIR__);

// Map page parameter to actual PHP file
$pageMap = [
    'index' => $basePath . '/index.php',
    'about' => $basePath . '/about.php',
    'contact' => $basePath . '/contact.php',
    'projects' => $basePath . '/projects.php',
];

// Validate and serve the requested page
if (isset($pageMap[$page]) && file_exists($pageMap[$page])) {
    // Pass through any query parameters
    require $pageMap[$page];
} else {
    // Default to index
    require $basePath . '/index.php';
}
