<?php
// Financial records API
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$action = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$user = getCurrentUser();

// Only healthcare professionals can update their own financial records
if (!isHealthcarePro()) {
    echo json_encode(['success' => false, 'error' => 'Only healthcare professionals can update financial records']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

switch ($action) {
    case 'update':
        $year = (int)($input['year'] ?? date('Y'));
        
        // Validate year is reasonable
        $currentYear = (int)date('Y');
        if ($year < 2000 || $year > $currentYear + 1) {
            echo json_encode(['success' => false, 'error' => 'Invalid year']);
            exit;
        }
        
        $data = [
            'total_income' => max(0, floatval($input['total_income'] ?? 0)),
            'total_expenses' => max(0, floatval($input['total_expenses'] ?? 0)),
            'tax_rate' => min(100, max(0, floatval($input['tax_rate'] ?? 25))),
            'notes' => isset($input['notes']) ? sanitize($input['notes']) : null
        ];
        
        $result = updateFinancialRecord($user['id'], $year, $data);
        
        echo json_encode(['success' => $result]);
        break;
        
    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
}
?>
