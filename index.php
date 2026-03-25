<?php
// Bootstrap: load DB and auth helpers
require_once __DIR__ . '/includes/db.php';

// Initialize database schema on first run
initializeDatabase();

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

// Parse the request URI (strip query string)
$requestUri  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestUri  = rtrim($requestUri, '/') ?: '/';
$method      = $_SERVER['REQUEST_METHOD'];

// ── API routes ──────────────────────────────────────────────────────────────
if (strpos($requestUri, '/api/') === 0) {
    $apiRoute = substr($requestUri, 5); // strip '/api/'
    
    switch ($apiRoute) {
        case 'auth':
            require __DIR__ . '/api/auth.php';
            break;
        case 'documents':
            require __DIR__ . '/api/documents.php';
            break;
        case 'financial':
            require __DIR__ . '/api/financial.php';
            break;
        case 'accountant':
            require __DIR__ . '/api/accountant.php';
            break;
        case 'client':
            require __DIR__ . '/api/client.php';
            break;
        case 'subscription':
            require __DIR__ . '/api/subscription.php';
            break;
        case 'admin':
            require __DIR__ . '/api/admin.php';
            break;
        case 'ai-reports':
            require __DIR__ . '/api/ai-reports.php';
            break;
        default:
            http_response_code(404);
            echo json_encode(['error' => 'API endpoint not found']);
            break;
    }
    exit;
}

// ── Page routes ──────────────────────────────────────────────────────────────
switch ($requestUri) {
    case '/':
        require __DIR__ . '/pages/landing.php';
        break;
    
    case '/login':
        require __DIR__ . '/pages/login.php';
        break;
    
    case '/register':
        require __DIR__ . '/pages/register.php';
        break;
    
    case '/logout':
        require __DIR__ . '/pages/logout.php';
        break;
    
    case '/dashboard':
        require __DIR__ . '/pages/dashboard.php';
        break;
    
    case '/profile':
        require __DIR__ . '/pages/profile.php';
        break;
    
    case '/documents':
        require __DIR__ . '/pages/documents.php';
        break;
    
    case '/accountants':
        require __DIR__ . '/pages/accountants.php';
        break;
    
    case '/clients':
        require __DIR__ . '/pages/clients.php';
        break;
    
    case '/subscription':
        require __DIR__ . '/pages/subscription.php';
        break;
    
    case '/reports':
        require __DIR__ . '/pages/reports.php';
        break;
    
    case '/ai-reports':
        require __DIR__ . '/pages/ai-reports.php';
        break;
    
    case '/admin':
        require __DIR__ . '/pages/admin.php';
        break;
    
    case '/admin/users':
        require __DIR__ . '/pages/admin-users.php';
        break;
    
    case '/admin/commissions':
        require __DIR__ . '/pages/admin-commissions.php';
        break;
    
    case '/report':
        require __DIR__ . '/meditax_report.html';
        break;
    
    default:
        http_response_code(404);
        require __DIR__ . '/pages/404.php';
        break;
}
?>
