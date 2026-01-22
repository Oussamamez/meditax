<?php
session_start();

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Get current user data
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

// Check if user has specific role
function hasRole($role) {
    $user = getCurrentUser();
    if (!$user) return false;
    
    if (is_array($role)) {
        return in_array($user['role'], $role);
    }
    return $user['role'] === $role;
}

// Check if user is healthcare professional
function isHealthcarePro() {
    return hasRole(['doctor', 'dentist', 'pharmacy']);
}

// Check if user is accountant
function isAccountant() {
    return hasRole('accountant');
}

// Check if user is admin
function isAdmin() {
    return hasRole('admin');
}

// Require authentication
function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: /login');
        exit;
    }
}

// Require specific role
function requireRole($role) {
    requireAuth();
    if (!hasRole($role)) {
        header('Location: /dashboard');
        exit;
    }
}

// Register new user
function registerUser($data) {
    $pdo = getDBConnection();
    
    // Check if email exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$data['email']]);
    if ($stmt->fetch()) {
        return ['success' => false, 'error' => 'Email already registered'];
    }
    
    // Hash password
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("
        INSERT INTO users (email, password, role, first_name, last_name, phone, business_name, license_number, specialty)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    try {
        $stmt->execute([
            $data['email'],
            $hashedPassword,
            $data['role'],
            $data['first_name'],
            $data['last_name'],
            $data['phone'] ?? null,
            $data['business_name'] ?? null,
            $data['license_number'] ?? null,
            $data['specialty'] ?? null
        ]);
        
        $userId = $pdo->lastInsertId();
        
        // If accountant, create inactive subscription
        if ($data['role'] === 'accountant') {
            $stmt = $pdo->prepare("INSERT INTO subscriptions (user_id, status) VALUES (?, 'inactive')");
            $stmt->execute([$userId]);
        }
        
        // Create initial financial record for current year
        if (in_array($data['role'], ['doctor', 'dentist', 'pharmacy'])) {
            $year = date('Y');
            $stmt = $pdo->prepare("INSERT INTO financial_records (user_id, year) VALUES (?, ?)");
            $stmt->execute([$userId, $year]);
        }
        
        return ['success' => true, 'user_id' => $userId];
    } catch (PDOException $e) {
        error_log("Registration error: " . $e->getMessage());
        return ['success' => false, 'error' => 'Registration failed. Please try again.'];
    }
}

// Login user
function loginUser($email, $password) {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user || !password_verify($password, $user['password'])) {
        return ['success' => false, 'error' => 'Invalid email or password'];
    }
    
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
    
    return ['success' => true, 'user' => $user];
}

// Logout user
function logoutUser() {
    session_destroy();
    header('Location: /');
    exit;
}

// CSRF token generation
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Validate CSRF token
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>
