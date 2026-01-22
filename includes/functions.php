<?php
// Sanitize input
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Format currency
function formatCurrency($amount) {
    return '$' . number_format($amount, 2);
}

// Format date
function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

// Calculate estimated tax (placeholder - 25% default rate)
function calculateEstimatedTax($income, $expenses, $rate = 25) {
    $profit = $income - $expenses;
    if ($profit <= 0) return 0;
    return $profit * ($rate / 100);
}

// Calculate commission (12%)
function calculateCommission($amount, $rate = 12) {
    return $amount * ($rate / 100);
}

// Get file extension
function getFileExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

// Check if file type is allowed
function isAllowedFileType($filename) {
    $allowed = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx'];
    return in_array(getFileExtension($filename), $allowed);
}

// Generate unique filename
function generateUniqueFilename($originalName) {
    $ext = getFileExtension($originalName);
    return uniqid() . '_' . time() . '.' . $ext;
}

// Get user's financial summary
function getFinancialSummary($userId, $year = null) {
    $pdo = getDBConnection();
    $year = $year ?? date('Y');
    
    $stmt = $pdo->prepare("SELECT * FROM financial_records WHERE user_id = ? AND year = ?");
    $stmt->execute([$userId, $year]);
    $record = $stmt->fetch();
    
    if (!$record) {
        return [
            'total_income' => 0,
            'total_expenses' => 0,
            'estimated_profit' => 0,
            'estimated_tax' => 0,
            'tax_rate' => 25
        ];
    }
    
    return $record;
}

// Get verified accountants list
function getVerifiedAccountants() {
    $pdo = getDBConnection();
    
    $stmt = $pdo->query("
        SELECT u.*, s.status as subscription_status 
        FROM users u
        LEFT JOIN subscriptions s ON u.id = s.user_id
        WHERE u.role = 'accountant' AND u.is_verified = true AND s.status = 'active'
        ORDER BY u.first_name, u.last_name
    ");
    
    return $stmt->fetchAll();
}

// Get accountant's clients
function getAccountantClients($accountantId) {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("
        SELECT u.*, ac.status as relationship_status, ac.year, ac.id as relationship_id
        FROM users u
        JOIN accountant_clients ac ON u.id = ac.client_id
        WHERE ac.accountant_id = ? AND ac.status IN ('pending', 'active')
        ORDER BY ac.created_at DESC
    ");
    $stmt->execute([$accountantId]);
    
    return $stmt->fetchAll();
}

// Get client's assigned accountant
function getClientAccountant($clientId, $year = null) {
    $pdo = getDBConnection();
    $year = $year ?? date('Y');
    
    $stmt = $pdo->prepare("
        SELECT u.*, ac.status as relationship_status
        FROM users u
        JOIN accountant_clients ac ON u.id = ac.accountant_id
        WHERE ac.client_id = ? AND ac.year = ? AND ac.status IN ('pending', 'active')
    ");
    $stmt->execute([$clientId, $year]);
    
    return $stmt->fetch();
}

// Get user's documents
function getUserDocuments($userId, $year = null) {
    $pdo = getDBConnection();
    
    if ($year) {
        $stmt = $pdo->prepare("SELECT * FROM documents WHERE user_id = ? AND year = ? ORDER BY uploaded_at DESC");
        $stmt->execute([$userId, $year]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM documents WHERE user_id = ? ORDER BY uploaded_at DESC");
        $stmt->execute([$userId]);
    }
    
    return $stmt->fetchAll();
}

// Get subscription status
function getSubscriptionStatus($userId) {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("SELECT * FROM subscriptions WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$userId]);
    
    return $stmt->fetch();
}

// Update financial record
function updateFinancialRecord($userId, $year, $data) {
    $pdo = getDBConnection();
    
    $profit = ($data['total_income'] ?? 0) - ($data['total_expenses'] ?? 0);
    $taxRate = $data['tax_rate'] ?? 25;
    $estimatedTax = calculateEstimatedTax($data['total_income'] ?? 0, $data['total_expenses'] ?? 0, $taxRate);
    
    $stmt = $pdo->prepare("
        INSERT INTO financial_records (user_id, year, total_income, total_expenses, estimated_profit, estimated_tax, tax_rate, notes)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ON CONFLICT (user_id, year) 
        DO UPDATE SET 
            total_income = EXCLUDED.total_income,
            total_expenses = EXCLUDED.total_expenses,
            estimated_profit = EXCLUDED.estimated_profit,
            estimated_tax = EXCLUDED.estimated_tax,
            tax_rate = EXCLUDED.tax_rate,
            notes = EXCLUDED.notes,
            updated_at = CURRENT_TIMESTAMP
    ");
    
    return $stmt->execute([
        $userId,
        $year,
        $data['total_income'] ?? 0,
        $data['total_expenses'] ?? 0,
        $profit,
        $estimatedTax,
        $taxRate,
        $data['notes'] ?? null
    ]);
}

// Get all users (admin)
function getAllUsers($role = null) {
    $pdo = getDBConnection();
    
    if ($role) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE role = ? ORDER BY created_at DESC");
        $stmt->execute([$role]);
    } else {
        $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
    }
    
    return $stmt->fetchAll();
}

// Get platform statistics (admin)
function getPlatformStats() {
    $pdo = getDBConnection();
    
    $stats = [];
    
    // Total users by role
    $stmt = $pdo->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
    $stats['users_by_role'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // Active subscriptions
    $stmt = $pdo->query("SELECT COUNT(*) FROM subscriptions WHERE status = 'active'");
    $stats['active_subscriptions'] = $stmt->fetchColumn();
    
    // Total commissions
    $stmt = $pdo->query("SELECT SUM(commission_amount) FROM commissions WHERE status = 'paid'");
    $stats['total_commissions'] = $stmt->fetchColumn() ?? 0;
    
    // Active contracts
    $stmt = $pdo->query("SELECT COUNT(*) FROM accountant_clients WHERE status = 'active'");
    $stats['active_contracts'] = $stmt->fetchColumn();
    
    return $stats;
}

// Flash message helpers
function setFlashMessage($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Role display name
function getRoleDisplayName($role) {
    $roles = [
        'doctor' => 'Doctor',
        'dentist' => 'Dentist',
        'pharmacy' => 'Pharmacy',
        'accountant' => 'Accountant',
        'admin' => 'Administrator'
    ];
    return $roles[$role] ?? ucfirst($role);
}
?>
