<?php
// Subscription management API
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$user = getCurrentUser();

if (!isAccountant()) {
    echo json_encode(['success' => false, 'error' => 'Only accountants can manage subscriptions']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$pdo = getDBConnection();

// Activate subscription (demo - in production would integrate with payment gateway)
$startDate = date('Y-m-d');
$endDate = date('Y-m-d', strtotime('+1 month'));

// Upsert subscription - INSERT if not exists, UPDATE if exists
$stmt = $pdo->prepare("
    INSERT INTO subscriptions (user_id, status, start_date, end_date, amount, plan_name)
    VALUES (?, 'active', ?, ?, 80.00, 'Professional')
    ON CONFLICT (user_id) 
    DO UPDATE SET 
        status = 'active',
        start_date = EXCLUDED.start_date,
        end_date = EXCLUDED.end_date,
        amount = EXCLUDED.amount
");

try {
    // First check if subscription table has unique constraint on user_id
    // If not, we need to handle it differently
    $checkStmt = $pdo->prepare("SELECT id FROM subscriptions WHERE user_id = ?");
    $checkStmt->execute([$user['id']]);
    $existing = $checkStmt->fetch();
    
    if ($existing) {
        // Update existing subscription
        $stmt = $pdo->prepare("
            UPDATE subscriptions 
            SET status = 'active', start_date = ?, end_date = ?, amount = 80.00
            WHERE user_id = ?
        ");
        $stmt->execute([$startDate, $endDate, $user['id']]);
    } else {
        // Insert new subscription
        $stmt = $pdo->prepare("
            INSERT INTO subscriptions (user_id, status, start_date, end_date, amount, plan_name)
            VALUES (?, 'active', ?, ?, 80.00, 'Professional')
        ");
        $stmt->execute([$user['id'], $startDate, $endDate]);
    }
    
    // Also verify the accountant automatically (demo)
    $stmt = $pdo->prepare("UPDATE users SET is_verified = true WHERE id = ?");
    $stmt->execute([$user['id']]);
    
    echo json_encode(['success' => true, 'message' => 'Subscription activated']);
} catch (PDOException $e) {
    error_log("Subscription error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Subscription activation failed']);
}
?>
