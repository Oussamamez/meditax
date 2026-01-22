<?php
// Accountant selection API
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$user = getCurrentUser();

if (!isHealthcarePro()) {
    echo json_encode(['success' => false, 'error' => 'Only healthcare professionals can select accountants']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$accountantId = (int)($input['accountant_id'] ?? 0);
$year = (int)($input['year'] ?? date('Y'));

if (!$accountantId) {
    echo json_encode(['success' => false, 'error' => 'Accountant ID required']);
    exit;
}

$pdo = getDBConnection();

// Verify accountant exists and is verified
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'accountant' AND is_verified = true");
$stmt->execute([$accountantId]);
$accountant = $stmt->fetch();

if (!$accountant) {
    echo json_encode(['success' => false, 'error' => 'Invalid or unverified accountant']);
    exit;
}

// Check for existing relationship
$stmt = $pdo->prepare("SELECT * FROM accountant_clients WHERE client_id = ? AND year = ?");
$stmt->execute([$user['id'], $year]);
$existing = $stmt->fetch();

if ($existing) {
    // Update existing relationship
    $stmt = $pdo->prepare("UPDATE accountant_clients SET accountant_id = ?, status = 'pending' WHERE id = ?");
    $stmt->execute([$accountantId, $existing['id']]);
} else {
    // Create new relationship
    $stmt = $pdo->prepare("INSERT INTO accountant_clients (accountant_id, client_id, year, status) VALUES (?, ?, ?, 'pending')");
    $stmt->execute([$accountantId, $user['id'], $year]);
}

echo json_encode(['success' => true]);
?>
