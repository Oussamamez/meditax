<?php
$pageTitle = 'Commission Tracking';
requireAuth();
requireRole('admin');

$pdo = getDBConnection();
$stmt = $pdo->query("
    SELECT c.*, 
           a.first_name as accountant_first_name, a.last_name as accountant_last_name,
           cl.first_name as client_first_name, cl.last_name as client_last_name
    FROM commissions c
    JOIN users a ON c.accountant_id = a.id
    JOIN users cl ON c.client_id = cl.id
    ORDER BY c.created_at DESC
");
$commissions = $stmt->fetchAll();

$totalPending = 0;
$totalPaid = 0;
foreach ($commissions as $c) {
    if ($c['status'] === 'pending') $totalPending += $c['commission_amount'];
    if ($c['status'] === 'paid') $totalPaid += $c['commission_amount'];
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Commission Tracking</h1>
        <p class="text-gray-600 mt-1">Track platform commissions (12% per contract)</p>
    </div>
    
    <!-- Summary Cards -->
    <div class="grid md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900"><?php echo formatCurrency($totalPending); ?></div>
            <div class="text-sm text-gray-600">Pending Commissions</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900"><?php echo formatCurrency($totalPaid); ?></div>
            <div class="text-sm text-gray-600">Paid Commissions</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-percentage text-purple-600 text-xl"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">12%</div>
            <div class="text-sm text-gray-600">Commission Rate</div>
        </div>
    </div>
    
    <!-- Commissions Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-semibold">All Commissions</h2>
        </div>
        
        <?php if (!empty($commissions)): ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-600">Accountant</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-600">Client</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-600">Contract Value</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-600">Commission (12%)</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-600">Year</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-600">Status</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commissions as $c): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-4 px-6">
                            <?php echo sanitize($c['accountant_first_name'] . ' ' . $c['accountant_last_name']); ?>
                        </td>
                        <td class="py-4 px-6">
                            <?php echo sanitize($c['client_first_name'] . ' ' . $c['client_last_name']); ?>
                        </td>
                        <td class="py-4 px-6"><?php echo formatCurrency($c['amount']); ?></td>
                        <td class="py-4 px-6 font-medium"><?php echo formatCurrency($c['commission_amount']); ?></td>
                        <td class="py-4 px-6"><?php echo $c['year']; ?></td>
                        <td class="py-4 px-6">
                            <span class="px-3 py-1 rounded-full text-sm <?php echo $c['status'] === 'paid' ? 'bg-green-100 text-green-700' : ($c['status'] === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700'); ?>">
                                <?php echo ucfirst($c['status']); ?>
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            <?php if ($c['status'] === 'pending'): ?>
                            <button class="px-3 py-1 bg-green-600 text-white rounded text-sm hover:bg-green-700 transition">
                                Mark Paid
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center py-12">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-dollar-sign text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Commissions Yet</h3>
            <p class="text-gray-600">Commissions will appear here when contracts are completed.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
