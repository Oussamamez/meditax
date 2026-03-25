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

// ===== AI FINANCIAL REPORTS FUNCTIONS =====

// Generate AI Financial Report
function generateAIFinancialReport($userId, $year, $reportType = 'comprehensive', $accountantId = null) {
    $pdo = getDBConnection();
    $financialData = getFinancialSummary($userId, $year);
    $documents = getUserDocuments($userId, $year);
    $currentUser = getCurrentUser();
    
    // Prepare data for AI analysis
    $analysisData = [
        'user' => [
            'name' => $currentUser['first_name'] . ' ' . $currentUser['last_name'],
            'role' => $currentUser['role'],
            'business' => $currentUser['business_name'] ?? 'Not specified'
        ],
        'year' => $year,
        'financial' => [
            'total_income' => $financialData['total_income'],
            'total_expenses' => $financialData['total_expenses'],
            'estimated_profit' => $financialData['estimated_profit'],
            'tax_rate' => $financialData['tax_rate'],
            'estimated_tax' => $financialData['estimated_tax']
        ],
        'document_count' => count($documents),
        'report_type' => $reportType
    ];
    
    // Generate AI content (can integrate with OpenAI API or other LLM)
    $aiContent = generateAIContent($analysisData);
    
    // Calculate key metrics
    $keyMetrics = [
        'income_to_expense_ratio' => $financialData['total_expenses'] > 0 ? 
            round($financialData['total_income'] / $financialData['total_expenses'], 2) : 0,
        'profit_margin' => $financialData['total_income'] > 0 ? 
            round(($financialData['estimated_profit'] / $financialData['total_income']) * 100, 2) : 0,
        'effective_tax_rate' => $financialData['estimated_profit'] > 0 ? 
            round(($financialData['estimated_tax'] / $financialData['estimated_profit']) * 100, 2) : 0,
        'growth_comparison' => rand(-15, 25) // Placeholder for year-over-year
    ];
    
    // Create charts data
    $chartsData = [
        'income_expense_chart' => [
            'income' => $financialData['total_income'],
            'expenses' => $financialData['total_expenses'],
            'profit' => $financialData['estimated_profit']
        ],
        'tax_breakdown' => [
            'income_tax' => $financialData['estimated_tax'],
            'other_taxes' => $financialData['estimated_tax'] * 0.15
        ]
    ];
    
    // Insert report into database
    try {
        $stmt = $pdo->prepare("
            INSERT INTO ai_financial_reports 
            (user_id, accountant_id, year, report_type, title, summary, detailed_analysis, 
             recommendations, key_metrics, charts_data, status, ai_model, generation_method, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $userId,
            $accountantId,
            $year,
            $reportType,
            $aiContent['title'],
            $aiContent['summary'],
            $aiContent['analysis'],
            $aiContent['recommendations'],
            json_encode($keyMetrics),
            json_encode($chartsData),
            'generated',
            'gpt-4-turbo', // Placeholder AI model
            'automated',
            getCurrentUser()['id']
        ]);
        
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        error_log("Error generating AI report: " . $e->getMessage());
        return false;
    }
}

// Generate AI Content (placeholder - can be replaced with actual API call)
function generateAIContent($analysisData) {
    $user = $analysisData['user'];
    $year = $analysisData['year'];
    $financial = $analysisData['financial'];
    
    $profitMargin = $financial['total_income'] > 0 ? 
        round(($financial['estimated_profit'] / $financial['total_income']) * 100, 1) : 0;
    
    $title = "AI Financial Analysis Report - {$user['name']} ({$year})";
    
    $summary = "Comprehensive financial analysis for {$user['name']}'s {$user['role']} practice in {$year}. " .
        "Total revenue of " . formatCurrency($financial['total_income']) . " with operating expenses of " .
        formatCurrency($financial['total_expenses']) . ", resulting in a profit of " .
        formatCurrency($financial['estimated_profit']) . " and an estimated tax liability of " .
        formatCurrency($financial['estimated_tax']) . ".";
    
    $analysis = "
    <h3 class='font-semibold text-lg mt-4 mb-2'>Financial Performance Analysis</h3>
    <p>Your practice demonstrated a profit margin of {$profitMargin}% in {$year}, which is indicative of a healthy business operation. With a total income of " . formatCurrency($financial['total_income']) . ", your expense management resulted in operating costs of " . formatCurrency($financial['total_expenses']) . ".</p>
    
    <h3 class='font-semibold text-lg mt-4 mb-2'>Revenue Insights</h3>
    <p>Your revenue streams show stability and growth potential. The documented income of " . formatCurrency($financial['total_income']) . " reflects your business activity during the fiscal year. Consider diversifying revenue streams if possible to enhance business resilience.</p>
    
    <h3 class='font-semibold text-lg mt-4 mb-2'>Expense Efficiency</h3>
    <p>Operating expenses of " . formatCurrency($financial['total_expenses']) . " represent " . 
    round(($financial['total_expenses'] / $financial['total_income']) * 100, 1) . "% of your total revenue. " .
    "This ratio is within acceptable ranges for healthcare professionals. Monitor expense categories for potential optimization.</p>
    
    <h3 class='font-semibold text-lg mt-4 mb-2'>Tax Liability Overview</h3>
    <p>Based on a " . $financial['tax_rate'] . "% tax rate and your estimated profit of " . 
    formatCurrency($financial['estimated_profit']) . ", your estimated tax liability is " . 
    formatCurrency($financial['estimated_tax']) . ". This calculation serves as an estimation for planning purposes.</p>
    ";
    
    $recommendations = "
    <ul class='list-disc list-inside space-y-2'>
        <li><strong>Tax Planning:</strong> Review deduction opportunities and consider quarterly estimated tax payments to avoid year-end surprises.</li>
        <li><strong>Expense Optimization:</strong> Conduct a detailed audit of expense categories to identify potential savings opportunities.</li>
        <li><strong>Documentation:</strong> Maintain comprehensive records of all business expenses to support tax deductions.</li>
        <li><strong>Growth Strategy:</strong> Based on current performance, consider strategic investments in equipment or marketing to boost revenue.</li>
        <li><strong>Financial Reserves:</strong> Build an emergency fund covering 3-6 months of operating expenses for business stability.</li>
        <li><strong>Regular Review:</strong> Schedule quarterly financial reviews with your accountant to monitor progress and adjust strategies.</li>
    </ul>
    ";
    
    return [
        'title' => $title,
        'summary' => $summary,
        'analysis' => $analysis,
        'recommendations' => $recommendations
    ];
}

// Get AI Financial Reports
function getAIFinancialReports($userId, $year = null, $status = null) {
    $pdo = getDBConnection();
    
    $query = "SELECT * FROM ai_financial_reports WHERE user_id = ?";
    $params = [$userId];
    
    if ($year) {
        $query .= " AND year = ?";
        $params[] = $year;
    }
    
    if ($status) {
        $query .= " AND status = ?";
        $params[] = $status;
    }
    
    $query .= " ORDER BY created_at DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// Get single AI Report
function getAIReport($reportId) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM ai_financial_reports WHERE id = ?");
    $stmt->execute([$reportId]);
    return $stmt->fetch();
}

// Update AI Report Status
function updateAIReportStatus($reportId, $status, $userId = null) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
        UPDATE ai_financial_reports 
        SET status = ?, updated_at = CURRENT_TIMESTAMP 
        WHERE id = ?
    ");
    return $stmt->execute([$status, $reportId]);
}

// Delete AI Report
function deleteAIReport($reportId) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("DELETE FROM ai_financial_reports WHERE id = ?");
    return $stmt->execute([$reportId]);
}

// Export AI Report to PDF (placeholder)
function exportAIReportToPDF($reportId) {
    $report = getAIReport($reportId);
    if (!$report) return false;
    
    // In production, use a PDF library like TCPDF or Dompdf
    // For now, return a simple HTML-to-PDF conversion instruction
    return [
        'filename' => 'ai_report_' . $report['id'] . '_' . $report['year'] . '.pdf',
        'title' => $report['title'],
        'content' => $report['detailed_analysis'] . $report['recommendations']
    ];
}

// Get AI Report Statistics
function getAIReportStats($userId, $year = null) {
    $pdo = getDBConnection();
    
    $query = "SELECT 
        COUNT(*) as total_reports,
        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_reports,
        SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft_reports,
        MAX(created_at) as last_generated
        FROM ai_financial_reports 
        WHERE user_id = ?";
    
    $params = [$userId];
    
    if ($year) {
        $query .= " AND year = ?";
        $params[] = $year;
    }
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetch();
}
?>
