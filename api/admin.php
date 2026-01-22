<?php
// Admin API endpoints
header('Content-Type: application/json');

if (!isLoggedIn() || !isAdmin()) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$action = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$input = json_decode(file_get_contents('php://input'), true);
$pdo = getDBConnection();

switch ($action) {
    case 'verify-user':
        $userId = (int)($input['user_id'] ?? 0);
        
        if (!$userId) {
            echo json_encode(['success' => false, 'error' => 'User ID required']);
            exit;
        }
        
        $stmt = $pdo->prepare("UPDATE users SET is_verified = true WHERE id = ?");
        $stmt->execute([$userId]);
        
        echo json_encode(['success' => true]);
        break;
        
    case 'update-user':
        $userId = (int)($input['user_id'] ?? 0);
        
        if (!$userId) {
            echo json_encode(['success' => false, 'error' => 'User ID required']);
            exit;
        }
        
        // In production, implement full user update logic
        echo json_encode(['success' => true]);
        break;
        
    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
}
?>
