<?php
// Client management API (for accountants)
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$user = getCurrentUser();

if (!isAccountant()) {
    echo json_encode(['success' => false, 'error' => 'Only accountants can manage clients']);
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
    case 'accept':
        $relationshipId = (int)($input['relationship_id'] ?? 0);
        
        if (!$relationshipId) {
            echo json_encode(['success' => false, 'error' => 'Relationship ID required']);
            exit;
        }
        
        // Verify ownership
        $stmt = $pdo->prepare("SELECT * FROM accountant_clients WHERE id = ? AND accountant_id = ?");
        $stmt->execute([$relationshipId, $user['id']]);
        $relationship = $stmt->fetch();
        
        if (!$relationship) {
            echo json_encode(['success' => false, 'error' => 'Relationship not found']);
            exit;
        }
        
        // Update status to active
        $stmt = $pdo->prepare("UPDATE accountant_clients SET status = 'active' WHERE id = ?");
        $stmt->execute([$relationshipId]);
        
        echo json_encode(['success' => true]);
        break;
        
    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
}
?>
