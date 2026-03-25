<?php
// AI Financial Reports API
header('Content-Type: application/json');

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$action = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$user = getCurrentUser();
$method = $_SERVER['REQUEST_METHOD'];

// Helper function for response
function respond($success, $data = null, $error = null) {
    echo json_encode([
        'success' => $success,
        'data' => $data,
        'error' => $error
    ]);
    exit;
}

switch ($action) {
    case 'ai-reports.php':
        // GET: Fetch AI reports
        if ($method === 'GET') {
            if (!isHealthcarePro()) {
                respond(false, null, 'Only healthcare professionals can view reports');
            }
            
            $year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
            $status = isset($_GET['status']) ? sanitize($_GET['status']) : null;
            
            $reports = getAIFinancialReports($user['id'], $year, $status);
            respond(true, ['reports' => $reports]);
        }
        // POST: Generate new AI report
        elseif ($method === 'POST') {
            if (!isHealthcarePro()) {
                respond(false, null, 'Only healthcare professionals can generate reports');
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            $year = isset($input['year']) ? (int)$input['year'] : date('Y');
            $reportType = isset($input['report_type']) ? sanitize($input['report_type']) : 'comprehensive';
            
            // Validate year
            $currentYear = (int)date('Y');
            if ($year < 2000 || $year > $currentYear + 1) {
                respond(false, null, 'Invalid year');
            }
            
            // Validate report type
            $validTypes = ['comprehensive', 'tax_summary', 'expense_analysis', 'growth_analysis', 'custom'];
            if (!in_array($reportType, $validTypes)) {
                respond(false, null, 'Invalid report type');
            }
            
            $reportId = generateAIFinancialReport($user['id'], $year, $reportType);
            
            if ($reportId) {
                respond(true, ['report_id' => $reportId, 'message' => 'Report generated successfully']);
            } else {
                respond(false, null, 'Failed to generate report');
            }
        }
        break;
    
    case 'get':
        // GET: Fetch specific report
        if ($method === 'GET') {
            $reportId = isset($_GET['id']) ? (int)$_GET['id'] : null;
            
            if (!$reportId) {
                respond(false, null, 'Report ID required');
            }
            
            $report = getAIReport($reportId);
            
            if (!$report) {
                respond(false, null, 'Report not found');
            }
            
            // Check authorization
            if ($report['user_id'] != $user['id'] && $report['accountant_id'] != $user['id'] && !isAdmin()) {
                respond(false, null, 'Unauthorized');
            }
            
            // Parse JSON fields
            $report['key_metrics'] = json_decode($report['key_metrics'], true);
            $report['charts_data'] = json_decode($report['charts_data'], true);
            
            respond(true, $report);
        }
        break;
    
    case 'update':
        // POST: Update report status
        if ($method === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $reportId = isset($input['id']) ? (int)$input['id'] : null;
            $status = isset($input['status']) ? sanitize($input['status']) : null;
            
            if (!$reportId || !$status) {
                respond(false, null, 'Report ID and status required');
            }
            
            $report = getAIReport($reportId);
            if (!$report) {
                respond(false, null, 'Report not found');
            }
            
            // Check authorization - only owner or accountant can update
            if ($report['user_id'] != $user['id'] && $report['accountant_id'] != $user['id'] && !isAdmin()) {
                respond(false, null, 'Unauthorized');
            }
            
            // Validate status
            $validStatuses = ['draft', 'generated', 'reviewed', 'approved', 'archived'];
            if (!in_array($status, $validStatuses)) {
                respond(false, null, 'Invalid status');
            }
            
            if (updateAIReportStatus($reportId, $status)) {
                respond(true, ['message' => 'Report status updated']);
            } else {
                respond(false, null, 'Failed to update report');
            }
        }
        break;
    
    case 'delete':
        // POST: Delete report
        if ($method === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $reportId = isset($input['id']) ? (int)$input['id'] : null;
            
            if (!$reportId) {
                respond(false, null, 'Report ID required');
            }
            
            $report = getAIReport($reportId);
            if (!$report) {
                respond(false, null, 'Report not found');
            }
            
            // Check authorization - only owner can delete
            if ($report['user_id'] != $user['id'] && !isAdmin()) {
                respond(false, null, 'Unauthorized');
            }
            
            if (deleteAIReport($reportId)) {
                respond(true, ['message' => 'Report deleted']);
            } else {
                respond(false, null, 'Failed to delete report');
            }
        }
        break;
    
    case 'stats':
        // GET: Get report statistics
        if ($method === 'GET') {
            if (!isHealthcarePro()) {
                respond(false, null, 'Only healthcare professionals can view stats');
            }
            
            $year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
            $stats = getAIReportStats($user['id'], $year);
            
            respond(true, $stats);
        }
        break;
    
    case 'export':
        // GET: Export report
        if ($method === 'GET') {
            $reportId = isset($_GET['id']) ? (int)$_GET['id'] : null;
            
            if (!$reportId) {
                respond(false, null, 'Report ID required');
            }
            
            $report = getAIReport($reportId);
            if (!$report) {
                respond(false, null, 'Report not found');
            }
            
            // Check authorization
            if ($report['user_id'] != $user['id'] && $report['accountant_id'] != $user['id'] && !isAdmin()) {
                respond(false, null, 'Unauthorized');
            }
            
            $exportData = exportAIReportToPDF($reportId);
            respond(true, $exportData);
        }
        break;
    
    default:
        respond(false, null, 'Invalid action');
}
?>
