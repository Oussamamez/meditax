<?php
$pageTitle = 'Documents';
requireAuth();

if (!isHealthcarePro()) {
    header('Location: /dashboard');
    exit;
}

$user = getCurrentUser();
$currentYear = date('Y');
$selectedYear = isset($_GET['year']) ? (int)$_GET['year'] : $currentYear;

$documents = getUserDocuments($user['id'], $selectedYear);
$financialSummary = getFinancialSummary($user['id'], $selectedYear);

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Document Management</h1>
            <p class="text-gray-600 mt-1">Upload and manage your financial documents</p>
        </div>
        <div class="mt-4 md:mt-0 flex items-center space-x-4">
            <select id="yearSelect" onchange="window.location.href='/documents?year='+this.value" 
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                <?php for ($y = $currentYear; $y >= $currentYear - 5; $y--): ?>
                    <option value="<?php echo $y; ?>" <?php echo $y == $selectedYear ? 'selected' : ''; ?>><?php echo $y; ?></option>
                <?php endfor; ?>
            </select>
            <button onclick="openUploadModal()" class="gradient-bg text-white px-4 py-2 rounded-lg font-medium hover:opacity-90 transition">
                <i class="fas fa-upload mr-2"></i>Upload Document
            </button>
        </div>
    </div>
    
    <!-- Financial Summary Card -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
        <h2 class="text-lg font-semibold mb-4">Financial Summary - <?php echo $selectedYear; ?></h2>
        <form id="financialForm" class="grid md:grid-cols-4 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Total Income</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                    <input type="number" id="total_income" name="total_income" step="0.01"
                           value="<?php echo $financialSummary['total_income']; ?>"
                           class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
                           onchange="updateFinancials()">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Total Expenses</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                    <input type="number" id="total_expenses" name="total_expenses" step="0.01"
                           value="<?php echo $financialSummary['total_expenses']; ?>"
                           class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
                           onchange="updateFinancials()">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estimated Profit</label>
                <div class="text-2xl font-bold text-green-600" id="estimatedProfit">
                    <?php echo formatCurrency($financialSummary['estimated_profit']); ?>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estimated Tax (<?php echo $financialSummary['tax_rate']; ?>%)</label>
                <div class="text-2xl font-bold text-purple-600" id="estimatedTax">
                    <?php echo formatCurrency($financialSummary['estimated_tax']); ?>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Documents Grid -->
    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Income Documents -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold flex items-center">
                    <span class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-2">
                        <i class="fas fa-arrow-up text-green-600"></i>
                    </span>
                    Income
                </h3>
                <span class="text-sm text-gray-500"><?php echo count(array_filter($documents, fn($d) => $d['category'] === 'income')); ?> files</span>
            </div>
            <div class="space-y-3 max-h-96 overflow-y-auto">
                <?php foreach ($documents as $doc): ?>
                    <?php if ($doc['category'] === 'income'): ?>
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg group">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-file-<?php echo getFileExtension($doc['filename']) === 'pdf' ? 'pdf' : 'image'; ?> text-green-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-sm truncate"><?php echo sanitize($doc['original_name']); ?></div>
                                <div class="text-xs text-gray-500"><?php echo formatDate($doc['uploaded_at']); ?></div>
                            </div>
                            <button onclick="deleteDocument(<?php echo $doc['id']; ?>)" class="opacity-0 group-hover:opacity-100 text-red-500 hover:text-red-700 transition">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php if (empty(array_filter($documents, fn($d) => $d['category'] === 'income'))): ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-file-upload text-3xl mb-2"></i>
                        <p class="text-sm">No income documents yet</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Expense Documents -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold flex items-center">
                    <span class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-2">
                        <i class="fas fa-arrow-down text-red-600"></i>
                    </span>
                    Expenses
                </h3>
                <span class="text-sm text-gray-500"><?php echo count(array_filter($documents, fn($d) => $d['category'] === 'expense')); ?> files</span>
            </div>
            <div class="space-y-3 max-h-96 overflow-y-auto">
                <?php foreach ($documents as $doc): ?>
                    <?php if ($doc['category'] === 'expense'): ?>
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg group">
                            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-file-<?php echo getFileExtension($doc['filename']) === 'pdf' ? 'pdf' : 'image'; ?> text-red-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-sm truncate"><?php echo sanitize($doc['original_name']); ?></div>
                                <div class="text-xs text-gray-500"><?php echo formatDate($doc['uploaded_at']); ?></div>
                            </div>
                            <button onclick="deleteDocument(<?php echo $doc['id']; ?>)" class="opacity-0 group-hover:opacity-100 text-red-500 hover:text-red-700 transition">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php if (empty(array_filter($documents, fn($d) => $d['category'] === 'expense'))): ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-file-upload text-3xl mb-2"></i>
                        <p class="text-sm">No expense documents yet</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Invoices -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold flex items-center">
                    <span class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-2">
                        <i class="fas fa-file-invoice text-blue-600"></i>
                    </span>
                    Invoices
                </h3>
                <span class="text-sm text-gray-500"><?php echo count(array_filter($documents, fn($d) => $d['category'] === 'invoice')); ?> files</span>
            </div>
            <div class="space-y-3 max-h-96 overflow-y-auto">
                <?php foreach ($documents as $doc): ?>
                    <?php if ($doc['category'] === 'invoice'): ?>
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg group">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-file-<?php echo getFileExtension($doc['filename']) === 'pdf' ? 'pdf' : 'image'; ?> text-blue-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-sm truncate"><?php echo sanitize($doc['original_name']); ?></div>
                                <div class="text-xs text-gray-500"><?php echo formatDate($doc['uploaded_at']); ?></div>
                            </div>
                            <button onclick="deleteDocument(<?php echo $doc['id']; ?>)" class="opacity-0 group-hover:opacity-100 text-red-500 hover:text-red-700 transition">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php if (empty(array_filter($documents, fn($d) => $d['category'] === 'invoice'))): ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-file-upload text-3xl mb-2"></i>
                        <p class="text-sm">No invoices yet</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div id="uploadModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold">Upload Document</h3>
            <button onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="uploadForm" enctype="multipart/form-data">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select name="category" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="income">Income</option>
                        <option value="expense">Expense</option>
                        <option value="invoice">Invoice</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">File</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary-500 transition cursor-pointer" onclick="document.getElementById('fileInput').click()">
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                        <p class="text-gray-600">Click to select or drag and drop</p>
                        <p class="text-sm text-gray-500">PDF, JPG, PNG, DOC, XLS (max 10MB)</p>
                        <input type="file" id="fileInput" name="document" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx" required class="hidden" onchange="updateFileName(this)">
                    </div>
                    <p id="selectedFile" class="text-sm text-primary-600 mt-2 hidden"></p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description (optional)</label>
                    <textarea name="description" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500" placeholder="Brief description..."></textarea>
                </div>
                
                <input type="hidden" name="year" value="<?php echo $selectedYear; ?>">
            </div>
            
            <div class="mt-6 flex space-x-3">
                <button type="button" onclick="closeUploadModal()" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="submit" class="flex-1 gradient-bg text-white px-4 py-3 rounded-lg font-medium hover:opacity-90 transition">
                    Upload
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openUploadModal() {
    document.getElementById('uploadModal').classList.remove('hidden');
}

function closeUploadModal() {
    document.getElementById('uploadModal').classList.add('hidden');
    document.getElementById('uploadForm').reset();
    document.getElementById('selectedFile').classList.add('hidden');
}

function updateFileName(input) {
    if (input.files.length > 0) {
        document.getElementById('selectedFile').textContent = 'Selected: ' + input.files[0].name;
        document.getElementById('selectedFile').classList.remove('hidden');
    }
}

document.getElementById('uploadForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    try {
        const response = await fetch('/api/documents/upload', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.error || 'Upload failed');
        }
    } catch (error) {
        alert('Upload failed. Please try again.');
    }
});

async function deleteDocument(id) {
    if (!confirm('Are you sure you want to delete this document?')) return;
    
    try {
        const response = await fetch('/api/documents/delete', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id: id})
        });
        const data = await response.json();
        
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.error || 'Delete failed');
        }
    } catch (error) {
        alert('Delete failed. Please try again.');
    }
}

async function updateFinancials() {
    const income = parseFloat(document.getElementById('total_income').value) || 0;
    const expenses = parseFloat(document.getElementById('total_expenses').value) || 0;
    const profit = income - expenses;
    const tax = profit > 0 ? profit * 0.25 : 0;
    
    document.getElementById('estimatedProfit').textContent = '$' + profit.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    document.getElementById('estimatedTax').textContent = '$' + tax.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    
    try {
        await fetch('/api/financial/update', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                year: <?php echo $selectedYear; ?>,
                total_income: income,
                total_expenses: expenses
            })
        });
    } catch (error) {
        console.error('Failed to save financials');
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
