<?php
// Authentication API endpoints
header('Content-Type: application/json');

$action = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

switch ($action) {
    case 'login':
        $email = $input['email'] ?? '';
        $password = $input['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'error' => 'Email and password required']);
            exit;
        }
        
        $result = loginUser($email, $password);
        echo json_encode($result);
        break;
        
    case 'register':
        $result = registerUser($input);
        echo json_encode($result);
        break;
        
    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
}
?>
