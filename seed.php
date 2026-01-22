<?php
// Seed script to populate database with demo data
require_once __DIR__ . '/includes/db.php';

echo "Initializing database...\n";
initializeDatabase();
echo "Database initialized!\n\n";

$pdo = getDBConnection();

// Check if admin already exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = 'admin@meditax.com'");
$stmt->execute();
if ($stmt->fetch()) {
    echo "Demo data already exists. Skipping seed.\n";
    exit;
}

echo "Creating demo users...\n";

// Create Admin
$adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
$stmt = $pdo->prepare("
    INSERT INTO users (email, password, role, first_name, last_name, is_verified)
    VALUES ('admin@meditax.com', ?, 'admin', 'System', 'Admin', true)
");
$stmt->execute([$adminPassword]);
echo "- Admin created: admin@meditax.com / admin123\n";

// Create demo accountants
$accountantPassword = password_hash('password123', PASSWORD_DEFAULT);

$accountants = [
    ['john.smith@accounting.com', 'John', 'Smith', 'Smith Tax Services', 'CPA-12345', 'Healthcare Tax Specialist'],
    ['sarah.wilson@finance.com', 'Sarah', 'Wilson', 'Wilson & Associates', 'CPA-67890', 'Medical Practice Accounting'],
    ['michael.brown@taxgroup.com', 'Michael', 'Brown', 'Brown Tax Group', 'CPA-11111', 'Pharmacy Financial Services'],
];

foreach ($accountants as $acc) {
    $stmt = $pdo->prepare("
        INSERT INTO users (email, password, role, first_name, last_name, business_name, license_number, specialty, is_verified)
        VALUES (?, ?, 'accountant', ?, ?, ?, ?, ?, true)
    ");
    $stmt->execute([$acc[0], $accountantPassword, $acc[1], $acc[2], $acc[3], $acc[4], $acc[5]]);
    $accountantId = $pdo->lastInsertId();
    
    // Create active subscription for each accountant
    $stmt = $pdo->prepare("
        INSERT INTO subscriptions (user_id, status, start_date, end_date, amount)
        VALUES (?, 'active', CURRENT_DATE, CURRENT_DATE + INTERVAL '30 days', 80.00)
    ");
    $stmt->execute([$accountantId]);
    
    echo "- Accountant created: {$acc[0]} / password123\n";
}

// Create demo healthcare professionals
$healthcarePassword = password_hash('password123', PASSWORD_DEFAULT);

$healthcare = [
    ['dr.james@clinic.com', 'doctor', 'James', 'Anderson', 'Anderson Family Clinic', 'MD-55555', 'General Practice'],
    ['dr.emily@dental.com', 'dentist', 'Emily', 'Chen', 'Bright Smile Dental', 'DDS-66666', 'Cosmetic Dentistry'],
    ['pharmacy@rxcare.com', 'pharmacy', 'Robert', 'Martinez', 'RxCare Pharmacy', 'RPH-77777', null],
    ['dr.lisa@cardio.com', 'doctor', 'Lisa', 'Thompson', 'Heart Health Cardiology', 'MD-88888', 'Cardiology'],
];

$currentYear = date('Y');

foreach ($healthcare as $hp) {
    $stmt = $pdo->prepare("
        INSERT INTO users (email, password, role, first_name, last_name, business_name, license_number, specialty, is_verified)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, true)
    ");
    $stmt->execute([$hp[0], $healthcarePassword, $hp[1], $hp[2], $hp[3], $hp[4], $hp[5], $hp[6]]);
    $healthcareId = $pdo->lastInsertId();
    
    // Create financial record with demo data
    $income = rand(150000, 500000);
    $expenses = rand(50000, 150000);
    $profit = $income - $expenses;
    $tax = $profit * 0.25;
    
    $stmt = $pdo->prepare("
        INSERT INTO financial_records (user_id, year, total_income, total_expenses, estimated_profit, estimated_tax, tax_rate)
        VALUES (?, ?, ?, ?, ?, ?, 25)
    ");
    $stmt->execute([$healthcareId, $currentYear, $income, $expenses, $profit, $tax]);
    
    echo "- Healthcare pro created: {$hp[0]} / password123\n";
}

// Create some sample accountant-client relationships
$stmt = $pdo->query("SELECT id FROM users WHERE role = 'accountant' ORDER BY id LIMIT 1");
$firstAccountant = $stmt->fetch();

$stmt = $pdo->query("SELECT id FROM users WHERE role IN ('doctor', 'dentist', 'pharmacy') ORDER BY id LIMIT 2");
$clients = $stmt->fetchAll();

if ($firstAccountant && count($clients) >= 2) {
    foreach ($clients as $client) {
        $stmt = $pdo->prepare("
            INSERT INTO accountant_clients (accountant_id, client_id, year, status)
            VALUES (?, ?, ?, 'active')
        ");
        $stmt->execute([$firstAccountant['id'], $client['id'], $currentYear]);
    }
    echo "\n- Created sample accountant-client relationships\n";
}

echo "\n======================================\n";
echo "Demo data created successfully!\n";
echo "======================================\n\n";
echo "Login credentials:\n";
echo "- Admin: admin@meditax.com / admin123\n";
echo "- Accountant: john.smith@accounting.com / password123\n";
echo "- Doctor: dr.james@clinic.com / password123\n";
echo "- Dentist: dr.emily@dental.com / password123\n";
echo "- Pharmacy: pharmacy@rxcare.com / password123\n";
?>
