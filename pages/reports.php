<?php
$pageTitle = 'Reports';
requireAuth();

if (!isHealthcarePro()) {
    header('Location: /dashboard');
    exit;
}

$user = getCurrentUser();
$currentYear = date('Y');
$selectedYear = isset($_GET['year']) ? (int)$_GET['year'] : $currentYear;

$financialSummary = getFinancialSummary($user['id'], $selectedYear);
$documents = getUserDocuments($user['id'], $selectedYear);
$accountant = getClientAccountant($user['id'], $selectedYear);

// Get tax reports from accountant
$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT * FROM tax_reports WHERE client_id = ? AND year = ? ORDER BY created_at DESC");
$stmt->execute([$user['id'], $selectedYear]);
$taxReports = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Financial Reports</h1>
            <p class="text-gray-600 mt-1">View your tax calculations and download reports</p>
        </div>
        <div class="mt-4 md:mt-0">
            <select id="yearSelect" onchange="window.location.href='/reports?year='+this.value" 
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                <?php for ($y = $currentYear; $y >= $currentYear - 5; $y--): ?>
                    <option value="<?php echo $y; ?>" <?php echo $y == $selectedYear ? 'selected' : ''; ?>><?php echo $y; ?></option>
                <?php endfor; ?>
            </select>
        </div>
    </div>
    
    <!-- Tax Summary Card -->
    <div class="bg-gradient-to-br from-primary-600 to-accent-600 rounded-2xl text-white p-8 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold">Tax Summary <?php echo $selectedYear; ?></h2>
            <span class="px-3 py-1 bg-white/20 rounded-full text-sm">
                <?php echo $financialSummary['tax_rate']; ?>% Tax Rate
            </span>
        </div>
        
        <div class="grid md:grid-cols-4 gap-6">
            <div>
                <div class="text-blue-100 text-sm mb-1">Total Income</div>
                <div class="text-3xl font-bold"><?php echo formatCurrency($financialSummary['total_income']); ?></div>
            </div>
            <div>
                <div class="text-blue-100 text-sm mb-1">Total Expenses</div>
                <div class="text-3xl font-bold"><?php echo formatCurrency($financialSummary['total_expenses']); ?></div>
            </div>
            <div>
                <div class="text-blue-100 text-sm mb-1">Taxable Income</div>
                <div class="text-3xl font-bold"><?php echo formatCurrency($financialSummary['estimated_profit']); ?></div>
            </div>
            <div>
                <div class="text-blue-100 text-sm mb-1">Estimated Tax Due</div>
                <div class="text-3xl font-bold"><?php echo formatCurrency($financialSummary['estimated_tax']); ?></div>
            </div>
        </div>
        
        <div class="mt-6 pt-6 border-t border-white/20">
            <p class="text-blue-100 text-sm">
                <i class="fas fa-info-circle mr-2"></i>
                This is an estimated calculation. Final tax liability may vary based on deductions, credits, and current tax laws. 
                Consult with your accountant for accurate figures.
            </p>
        </div>
    </div>
    
    <div class="grid lg:grid-cols-2 gap-8">
        <!-- Tax Reports from Accountant -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold mb-4">Tax Reports</h2>
            
            <?php if (!empty($taxReports)): ?>
            <div class="space-y-4">
                <?php foreach ($taxReports as $report): ?>
                <div class="border rounded-lg p-4">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="font-medium">Tax Report - <?php echo $report['year']; ?></h3>
                            <p class="text-sm text-gray-500">Created <?php echo formatDate($report['created_at']); ?></p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full 
                            <?php echo $report['status'] === 'approved' ? 'bg-green-100 text-green-700' : 
                                ($report['status'] === 'submitted' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700'); ?>">
                            <?php echo ucfirst($report['status']); ?>
                        </span>
                    </div>
                    
                    <?php if ($report['summary']): ?>
                    <p class="text-sm text-gray-600 mb-3"><?php echo sanitize($report['summary']); ?></p>
                    <?php endif; ?>
                    
                    <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                        <div>
                            <span class="text-gray-500">Taxable Income:</span>
                            <span class="font-medium"><?php echo formatCurrency($report['taxable_income'] ?? 0); ?></span>
                        </div>
                        <div>
                            <span class="text-gray-500">Tax Liability:</span>
                            <span class="font-medium"><?php echo formatCurrency($report['tax_liability'] ?? 0); ?></span>
                        </div>
                    </div>
                    
                    <?php if ($report['report_file']): ?>
                    <a href="/uploads/documents/<?php echo $report['report_file']; ?>" 
                       class="inline-flex items-center text-primary-600 hover:text-primary-700 text-sm">
                        <i class="fas fa-download mr-2"></i>Download Report PDF
                    </a>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-file-alt text-gray-400 text-2xl"></i>
                </div>
                <h3 class="font-medium text-gray-900 mb-2">No Tax Reports Yet</h3>
                <p class="text-gray-600 text-sm">
                    <?php if ($accountant): ?>
                        Your accountant will prepare and upload tax reports here.
                    <?php else: ?>
                        <a href="/accountants" class="text-primary-600 hover:underline">Select an accountant</a> to get your tax reports prepared.
                    <?php endif; ?>
                </p>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Generate Quick Report -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold mb-4">Generate Quick Report</h2>
            
            <div class="space-y-4 mb-6">
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-file-pdf text-primary-600"></i>
                        </div>
                        <div>
                            <div class="font-medium">Income Summary</div>
                            <div class="text-sm text-gray-500">Overview of all income for <?php echo $selectedYear; ?></div>
                        </div>
                    </div>
                    <button onclick="generateReport('income')" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition text-sm">
                        Generate
                    </button>
                </div>
                
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-file-pdf text-red-600"></i>
                        </div>
                        <div>
                            <div class="font-medium">Expense Summary</div>
                            <div class="text-sm text-gray-500">All deductible expenses for <?php echo $selectedYear; ?></div>
                        </div>
                    </div>
                    <button onclick="generateReport('expense')" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition text-sm">
                        Generate
                    </button>
                </div>
                
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-file-pdf text-purple-600"></i>
                        </div>
                        <div>
                            <div class="font-medium">Full Financial Report</div>
                            <div class="text-sm text-gray-500">Complete financial overview</div>
                        </div>
                    </div>
                    <button onclick="generateReport('full')" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition text-sm">
                        Generate
                    </button>
                </div>
            </div>
            
            <div class="bg-blue-50 rounded-lg p-4 text-sm text-blue-800">
                <i class="fas fa-info-circle mr-2"></i>
                Quick reports are auto-generated summaries. For official tax documents, please work with your assigned accountant.
            </div>
        </div>
    </div>
    
    <!-- Document Summary -->
    <div class="mt-8 bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-semibold mb-4">Document Summary</h2>
        <div class="grid md:grid-cols-4 gap-4">
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-3xl font-bold text-gray-900"><?php echo count($documents); ?></div>
                <div class="text-sm text-gray-600">Total Documents</div>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <div class="text-3xl font-bold text-green-700"><?php echo count(array_filter($documents, fn($d) => $d['category'] === 'income')); ?></div>
                <div class="text-sm text-green-600">Income Records</div>
            </div>
            <div class="text-center p-4 bg-red-50 rounded-lg">
                <div class="text-3xl font-bold text-red-700"><?php echo count(array_filter($documents, fn($d) => $d['category'] === 'expense')); ?></div>
                <div class="text-sm text-red-600">Expense Records</div>
            </div>
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <div class="text-3xl font-bold text-blue-700"><?php echo count(array_filter($documents, fn($d) => $d['category'] === 'invoice')); ?></div>
                <div class="text-sm text-blue-600">Invoices</div>
            </div>
        </div>
    </div>
</div>

<script>
function generateReport(type) {
    alert('Report generation is a demo feature. In production, this would generate a PDF report for: ' + type);
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
