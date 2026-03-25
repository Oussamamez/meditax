<?php
// Main router for MediTax Connect
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

// Initialize database on first run
try {
    initializeDatabase();
} catch (Exception $e) {
    error_log("Database initialization error: " . $e->getMessage());
}

// Get the request URI and parse it
$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);
$path = rtrim($path, '/') ?: '/';

// Redirect logged-in users from landing to dashboard
if ($path === '/' && isLoggedIn()) {
    header('Location: /dashboard');
    exit;
}

// Simple router
$routes = [
    '/' => 'pages/landing.php',
    '/login' => 'pages/login.php',
    '/register' => 'pages/register.php',
    '/logout' => 'pages/logout.php',
    '/dashboard' => 'pages/dashboard.php',
    '/profile' => 'pages/profile.php',
    '/documents' => 'pages/documents.php',
    '/accountants' => 'pages/accountants.php',
    '/clients' => 'pages/clients.php',
    '/reports' => 'pages/reports.php',
    '/ai-reports' => 'pages/ai-reports.php',
    '/subscription' => 'pages/subscription.php',
    '/admin' => 'pages/admin.php',
    '/admin/users' => 'pages/admin-users.php',
    '/admin/commissions' => 'pages/admin-commissions.php',
];

// API routes
$apiRoutes = [
    '/api/auth/login' => 'api/auth.php',
    '/api/auth/register' => 'api/auth.php',
    '/api/documents/upload' => 'api/documents.php',
    '/api/documents/delete' => 'api/documents.php',
    '/api/financial/update' => 'api/financial.php',
    '/api/accountant/select' => 'api/accountant.php',
    '/api/client/accept' => 'api/client.php',
    '/api/subscription/activate' => 'api/subscription.php',
    '/api/admin/verify-user' => 'api/admin.php',
    '/api/admin/update-user' => 'api/admin.php',
    '/api/ai-reports.php' => 'api/ai-reports.php',
];

// Check for API routes first
foreach ($apiRoutes as $route => $file) {
    if (strpos($path, $route) === 0) {
        require_once __DIR__ . '/' . $file;
        exit;
    }
}

// Check for page routes
if (isset($routes[$path])) {
    require_once __DIR__ . '/' . $routes[$path];
} else {
    // 404 Not Found
    http_response_code(404);
    require_once __DIR__ . '/pages/404.php';
}
?>
