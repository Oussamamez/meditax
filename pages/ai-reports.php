<?php
$pageTitle = 'AI Financial Reports';
requireAuth();

if (!isHealthcarePro()) {
    header('Location: /dashboard');
    exit;
}

$user = getCurrentUser();
$currentYear = date('Y');
$selectedYear = isset($_GET['year']) ? (int)$_GET['year'] : $currentYear;

// Get AI reports
$aiReports = getAIFinancialReports($user['id'], $selectedYear);
$reportStats = getAIReportStats($user['id'], $selectedYear);

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">AI Financial Reports</h1>
            <p class="text-gray-600 mt-1">AI-powered analysis of your financial data</p>
        </div>
        <div class="mt-4 md:mt-0 flex gap-4">
            <select id="yearSelect" onchange="window.location.href='/ai-reports?year='+this.value" 
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                <?php for ($y = $currentYear; $y >= $currentYear - 5; $y--): ?>
                    <option value="<?php echo $y; ?>" <?php echo $y == $selectedYear ? 'selected' : ''; ?>><?php echo $y; ?></option>
                <?php endfor; ?>
            </select>
            <button onclick="generateNewReport()" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                <i class="fas fa-magic mr-2"></i>Generate New Report
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Reports</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo $reportStats['total_reports'] ?? 0; ?></p>
                </div>
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i class="fas fa-file-chart-line text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Approved</p>
                    <p class="text-3xl font-bold text-green-600"><?php echo $reportStats['approved_reports'] ?? 0; ?></p>
                </div>
                <div class="p-3 bg-green-100 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Drafts</p>
                    <p class="text-3xl font-bold text-yellow-600"><?php echo $reportStats['draft_reports'] ?? 0; ?></p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-lg">
                    <i class="fas fa-edit text-yellow-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Last Generated</p>
                    <p class="text-lg font-semibold text-gray-900">
                        <?php echo $reportStats['last_generated'] ? 
                            date('M d, Y', strtotime($reportStats['last_generated'])) : 'Never'; ?>
                    </p>
                </div>
                <div class="p-3 bg-purple-100 rounded-lg">
                    <i class="fas fa-calendar text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Tabs -->
    <div class="bg-white rounded-lg shadow-sm mb-6">
        <div class="border-b border-gray-200 px-6">
            <div class="flex gap-8">
                <button class="tab-btn active py-4 px-1 border-b-2 border-primary-600 text-primary-600 font-medium" data-tab="all">
                    All Reports
                </button>
                <button class="tab-btn py-4 px-1 border-b-2 border-transparent text-gray-600 hover:text-gray-900" data-tab="approved">
                    Approved
                </button>
                <button class="tab-btn py-4 px-1 border-b-2 border-transparent text-gray-600 hover:text-gray-900" data-tab="draft">
                    Drafts
                </button>
            </div>
        </div>
    </div>

    <!-- Reports List -->
    <div class="space-y-4">
        <?php if (empty($aiReports)): ?>
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <i class="fas fa-chart-line text-gray-300 text-5xl mb-4 block"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No AI Reports Yet</h3>
            <p class="text-gray-600 mb-6">Generate your first AI-powered financial report to get started with intelligent financial analysis.</p>
            <button onclick="generateNewReport()" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                Generate First Report
            </button>
        </div>
        <?php else: ?>
            <?php foreach ($aiReports as $report): ?>
            <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition report-card" data-status="<?php echo $report['status']; ?>">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3">
                            <h3 class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($report['title']); ?></h3>
                            <span class="px-3 py-1 text-xs rounded-full font-medium
                                <?php 
                                $statusColors = [
                                    'draft' => 'bg-yellow-100 text-yellow-700',
                                    'generated' => 'bg-blue-100 text-blue-700',
                                    'reviewed' => 'bg-purple-100 text-purple-700',
                                    'approved' => 'bg-green-100 text-green-700',
                                    'archived' => 'bg-gray-100 text-gray-700'
                                ];
                                echo $statusColors[$report['status']] ?? 'bg-gray-100 text-gray-700';
                                ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $report['status'])); ?>
                            </span>
                        </div>
                        <p class="text-gray-600 text-sm mt-1">Report Type: <strong><?php echo ucfirst(str_replace('_', ' ', $report['report_type'])); ?></strong></p>
                        <p class="text-gray-500 text-sm">Generated on <?php echo formatDate($report['created_at']); ?></p>
                    </div>
                    <div class="flex gap-2 ml-4">
                        <button onclick="viewReport(<?php echo $report['id']; ?>)" class="px-4 py-2 text-primary-600 hover:bg-primary-50 rounded-lg transition" title="View Report">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button onclick="downloadReport(<?php echo $report['id']; ?>)" class="px-4 py-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Download PDF">
                            <i class="fas fa-download"></i>
                        </button>
                        <button onclick="deleteReport(<?php echo $report['id']; ?>)" class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete Report">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>

                <p class="text-gray-700 mb-4 line-clamp-2"><?php echo htmlspecialchars($report['summary']); ?></p>

                <!-- Key Metrics Preview -->
                <?php if ($report['key_metrics']): ?>
                    <?php $metrics = json_decode($report['key_metrics'], true); ?>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                    <?php if (isset($metrics['profit_margin'])): ?>
                    <div class="bg-blue-50 rounded-lg p-3">
                        <p class="text-gray-600 text-xs">Profit Margin</p>
                        <p class="text-lg font-bold text-blue-600"><?php echo $metrics['profit_margin']; ?>%</p>
                    </div>
                    <?php endif; ?>
                    <?php if (isset($metrics['income_to_expense_ratio'])): ?>
                    <div class="bg-green-50 rounded-lg p-3">
                        <p class="text-gray-600 text-xs">Income/Expense Ratio</p>
                        <p class="text-lg font-bold text-green-600"><?php echo $metrics['income_to_expense_ratio']; ?>x</p>
                    </div>
                    <?php endif; ?>
                    <?php if (isset($metrics['effective_tax_rate'])): ?>
                    <div class="bg-orange-50 rounded-lg p-3">
                        <p class="text-gray-600 text-xs">Effective Tax Rate</p>
                        <p class="text-lg font-bold text-orange-600"><?php echo $metrics['effective_tax_rate']; ?>%</p>
                    </div>
                    <?php endif; ?>
                    <?php if (isset($metrics['growth_comparison'])): ?>
                    <div class="bg-purple-50 rounded-lg p-3">
                        <p class="text-gray-600 text-xs">YoY Growth</p>
                        <p class="text-lg font-bold <?php echo $metrics['growth_comparison'] >= 0 ? 'text-green-600' : 'text-red-600'; ?>">
                            <?php echo ($metrics['growth_comparison'] >= 0 ? '+' : ''); ?><?php echo $metrics['growth_comparison']; ?>%
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Generate Report Modal -->
<div id="generateModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4">
        <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="text-xl font-bold text-gray-900">Generate New AI Report</h2>
        </div>
        <form id="generateForm" onsubmit="submitGenerateReport(event)" class="p-6">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Report Type</label>
                <select id="reportType" name="report_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500" required>
                    <option value="comprehensive">Comprehensive Analysis</option>
                    <option value="tax_summary">Tax Summary</option>
                    <option value="expense_analysis">Expense Analysis</option>
                    <option value="growth_analysis">Growth Analysis</option>
                    <option value="custom">Custom Report</option>
                </select>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                <select id="reportYear" name="year" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500" required>
                    <?php for ($y = $currentYear; $y >= $currentYear - 5; $y--): ?>
                        <option value="<?php echo $y; ?>" <?php echo $y == $selectedYear ? 'selected' : ''; ?>><?php echo $y; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('generateModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                    Generate Report
                </button>
            </div>
        </form>
    </div>
</div>

<!-- View Report Modal -->
<div id="viewModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 overflow-y-auto">
    <div class="bg-white rounded-lg shadow-lg max-w-4xl w-full mx-4 my-8">
        <div class="sticky top-0 border-b border-gray-200 px-6 py-4 bg-white rounded-t-lg flex justify-between items-center">
            <h2 id="viewTitle" class="text-xl font-bold text-gray-900"></h2>
            <button onclick="document.getElementById('viewModal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        <div id="viewContent" class="p-6 max-h-[calc(100vh-200px)] overflow-y-auto">
            <div class="text-center text-gray-500 py-8">
                <i class="fas fa-spinner fa-spin text-3xl"></i>
                <p class="mt-2">Loading report...</p>
            </div>
        </div>
    </div>
</div>

<!-- Loading Indicator -->
<div id="loadingIndicator" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-8 text-center">
        <i class="fas fa-spinner fa-spin text-4xl text-primary-600 mb-4"></i>
        <p class="text-gray-700 font-medium">Generating AI Report...</p>
        <p class="text-gray-500 text-sm mt-2">This may take a moment</p>
    </div>
</div>

<script>
function generateNewReport() {
    document.getElementById('generateModal').classList.remove('hidden');
}

function submitGenerateReport(event) {
    event.preventDefault();
    
    const reportType = document.getElementById('reportType').value;
    const year = document.getElementById('reportYear').value;
    
    document.getElementById('generateModal').classList.add('hidden');
    document.getElementById('loadingIndicator').classList.remove('hidden');
    
    fetch('/api/ai-reports.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            report_type: reportType,
            year: year
        })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('loadingIndicator').classList.add('hidden');
        
        if (data.success) {
            alert('Report generated successfully!');
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'Failed to generate report'));
        }
    })
    .catch(error => {
        document.getElementById('loadingIndicator').classList.add('hidden');
        alert('Error: ' + error.message);
    });
}

function viewReport(reportId) {
    document.getElementById('viewModal').classList.remove('hidden');
    
    fetch(`/api/ai-reports.php?action=get&id=${reportId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const report = data.data;
            document.getElementById('viewTitle').textContent = report.title;
            
            const html = `
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Summary</h3>
                        <p class="text-gray-700">${report.summary}</p>
                    </div>
                    
                    ${report.detailed_analysis ? `
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Detailed Analysis</h3>
                        <div class="prose max-w-none text-gray-700">
                            ${report.detailed_analysis}
                        </div>
                    </div>
                    ` : ''}
                    
                    ${report.recommendations ? `
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Recommendations</h3>
                        <div class="prose max-w-none text-gray-700">
                            ${report.recommendations}
                        </div>
                    </div>
                    ` : ''}
                    
                    ${report.key_metrics ? `
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Key Metrics</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            ${Object.entries(report.key_metrics).map(([key, value]) => `
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-gray-600 text-sm">${key.replace(/_/g, ' ')}</p>
                                    <p class="text-2xl font-bold text-primary-600">${value}</p>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    ` : ''}
                </div>
            `;
            
            document.getElementById('viewContent').innerHTML = html;
        } else {
            document.getElementById('viewContent').innerHTML = `<p class="text-red-600">Error loading report: ${data.error}</p>`;
        }
    })
    .catch(error => {
        document.getElementById('viewContent').innerHTML = `<p class="text-red-600">Error: ${error.message}</p>`;
    });
}

function downloadReport(reportId) {
    // In production, implement PDF download
    alert('PDF export feature coming soon!');
}

function deleteReport(reportId) {
    if (!confirm('Are you sure you want to delete this report? This action cannot be undone.')) {
        return;
    }
    
    fetch('/api/ai-reports.php?action=delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ id: reportId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Report deleted successfully');
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'Failed to delete report'));
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}

// Tab switching
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const tab = this.dataset.tab;
        
        // Update active tab button
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('active', 'border-b-2', 'border-primary-600', 'text-primary-600');
            b.classList.add('border-b-2', 'border-transparent');
        });
        this.classList.add('active', 'border-b-2', 'border-primary-600', 'text-primary-600');
        this.classList.remove('border-b-2', 'border-transparent');
        
        // Filter reports
        document.querySelectorAll('.report-card').forEach(card => {
            if (tab === 'all') {
                card.style.display = 'block';
            } else {
                card.style.display = card.dataset.status === tab ? 'block' : 'none';
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
