<?php
$pageTitle = 'Dashboard';
requireAuth();

$user = getCurrentUser();
$role = $user['role'];

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav.php';

// Get role-specific data
$currentYear = date('Y');

if (isHealthcarePro()) {
    $financialSummary = getFinancialSummary($user['id'], $currentYear);
    $documents = getUserDocuments($user['id'], $currentYear);
    $accountant = getClientAccountant($user['id'], $currentYear);
} elseif (isAccountant()) {
    $subscription = getSubscriptionStatus($user['id']);
    $clients = getAccountantClients($user['id']);
} elseif (isAdmin()) {
    $stats = getPlatformStats();
}
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Welcome Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            Welcome back, <?php echo sanitize($user['first_name']); ?>!
        </h1>
        <p class="text-gray-600 mt-1">
            <?php echo getRoleDisplayName($role); ?> Dashboard 
            <?php if ($user['business_name']): ?>
                - <?php echo sanitize($user['business_name']); ?>
            <?php endif; ?>
        </p>
    </div>
    
    <?php if (isHealthcarePro()): ?>
    <!-- Healthcare Professional Dashboard -->
    <div class="grid lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                </div>
                <span class="text-sm text-gray-500"><?php echo $currentYear; ?></span>
            </div>
            <div class="text-2xl font-bold text-gray-900"><?php echo formatCurrency($financialSummary['total_income']); ?></div>
            <div class="text-sm text-gray-600">Total Income</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-receipt text-red-600 text-xl"></i>
                </div>
                <span class="text-sm text-gray-500"><?php echo $currentYear; ?></span>
            </div>
            <div class="text-2xl font-bold text-gray-900"><?php echo formatCurrency($financialSummary['total_expenses']); ?></div>
            <div class="text-sm text-gray-600">Total Expenses</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                </div>
                <span class="text-sm text-gray-500"><?php echo $currentYear; ?></span>
            </div>
            <div class="text-2xl font-bold text-gray-900"><?php echo formatCurrency($financialSummary['estimated_profit']); ?></div>
            <div class="text-sm text-gray-600">Estimated Profit</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-invoice-dollar text-purple-600 text-xl"></i>
                </div>
                <span class="text-sm text-gray-500"><?php echo $financialSummary['tax_rate']; ?>% rate</span>
            </div>
            <div class="text-2xl font-bold text-gray-900"><?php echo formatCurrency($financialSummary['estimated_tax']); ?></div>
            <div class="text-sm text-gray-600">Estimated Tax</div>
        </div>
    </div>
    
    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold mb-4">Quick Actions</h2>
            <div class="space-y-3">
                <a href="/documents" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-upload text-primary-600"></i>
                    </div>
                    <div>
                        <div class="font-medium">Upload Documents</div>
                        <div class="text-sm text-gray-500">Add income, expenses, invoices</div>
                    </div>
                </a>
                <a href="/accountants" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="w-10 h-10 bg-accent-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-user-tie text-accent-600"></i>
                    </div>
                    <div>
                        <div class="font-medium">Find Accountant</div>
                        <div class="text-sm text-gray-500">Browse verified professionals</div>
                    </div>
                </a>
                <a href="/reports" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-file-pdf text-purple-600"></i>
                    </div>
                    <div>
                        <div class="font-medium">View Reports</div>
                        <div class="text-sm text-gray-500">Download tax reports</div>
                    </div>
                </a>
            </div>
        </div>
        
        <!-- Assigned Accountant -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold mb-4">Your Accountant</h2>
            <?php if ($accountant): ?>
                <div class="flex items-center mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-primary-500 to-accent-500 rounded-full flex items-center justify-center text-white text-xl font-bold mr-4">
                        <?php echo strtoupper(substr($accountant['first_name'], 0, 1) . substr($accountant['last_name'], 0, 1)); ?>
                    </div>
                    <div>
                        <div class="font-semibold"><?php echo sanitize($accountant['first_name'] . ' ' . $accountant['last_name']); ?></div>
                        <div class="text-sm text-gray-500"><?php echo sanitize($accountant['business_name'] ?? 'Independent Accountant'); ?></div>
                        <?php if ($accountant['is_verified']): ?>
                            <span class="inline-flex items-center text-xs text-green-600"><i class="fas fa-check-circle mr-1"></i> Verified</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="text-sm text-gray-600 mb-4">
                    <div class="flex items-center mb-1"><i class="fas fa-envelope mr-2 text-gray-400"></i> <?php echo sanitize($accountant['email']); ?></div>
                    <?php if ($accountant['phone']): ?>
                        <div class="flex items-center"><i class="fas fa-phone mr-2 text-gray-400"></i> <?php echo sanitize($accountant['phone']); ?></div>
                    <?php endif; ?>
                </div>
                <span class="inline-block px-3 py-1 bg-<?php echo $accountant['relationship_status'] === 'active' ? 'green' : 'yellow'; ?>-100 text-<?php echo $accountant['relationship_status'] === 'active' ? 'green' : 'yellow'; ?>-700 rounded-full text-sm">
                    <?php echo ucfirst($accountant['relationship_status']); ?>
                </span>
            <?php else: ?>
                <div class="text-center py-6">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-plus text-gray-400 text-2xl"></i>
                    </div>
                    <p class="text-gray-600 mb-4">No accountant selected for <?php echo $currentYear; ?></p>
                    <a href="/accountants" class="inline-block bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition">
                        Find an Accountant
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Recent Documents -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">Recent Documents</h2>
                <a href="/documents" class="text-sm text-primary-600 hover:text-primary-700">View all</a>
            </div>
            <?php if (!empty($documents)): ?>
                <div class="space-y-3">
                    <?php foreach (array_slice($documents, 0, 4) as $doc): ?>
                        <div class="flex items-center p-2 bg-gray-50 rounded-lg">
                            <div class="w-10 h-10 bg-<?php echo $doc['category'] === 'income' ? 'green' : ($doc['category'] === 'expense' ? 'red' : 'blue'); ?>-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-file-<?php echo in_array(getFileExtension($doc['filename']), ['pdf']) ? 'pdf' : 'image'; ?> text-<?php echo $doc['category'] === 'income' ? 'green' : ($doc['category'] === 'expense' ? 'red' : 'blue'); ?>-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-sm truncate"><?php echo sanitize($doc['original_name']); ?></div>
                                <div class="text-xs text-gray-500"><?php echo formatDate($doc['uploaded_at']); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-6">
                    <p class="text-gray-500 text-sm">No documents uploaded yet</p>
                    <a href="/documents" class="text-sm text-primary-600 hover:underline">Upload your first document</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php elseif (isAccountant()): ?>
    <!-- Accountant Dashboard -->
    <div class="grid lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-<?php echo ($subscription && $subscription['status'] === 'active') ? 'green' : 'yellow'; ?>-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-credit-card text-<?php echo ($subscription && $subscription['status'] === 'active') ? 'green' : 'yellow'; ?>-600 text-xl"></i>
                </div>
            </div>
            <div class="text-xl font-bold text-gray-900"><?php echo ($subscription && $subscription['status'] === 'active') ? 'Active' : 'Inactive'; ?></div>
            <div class="text-sm text-gray-600">Subscription Status</div>
            <?php if (!$subscription || $subscription['status'] !== 'active'): ?>
                <a href="/subscription" class="mt-3 inline-block text-sm text-primary-600 hover:underline">Activate Now</a>
            <?php endif; ?>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900"><?php echo count($clients); ?></div>
            <div class="text-sm text-gray-600">Active Clients</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-<?php echo $user['is_verified'] ? 'green' : 'yellow'; ?>-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-<?php echo $user['is_verified'] ? 'check-circle' : 'clock'; ?> text-<?php echo $user['is_verified'] ? 'green' : 'yellow'; ?>-600 text-xl"></i>
                </div>
            </div>
            <div class="text-xl font-bold text-gray-900"><?php echo $user['is_verified'] ? 'Verified' : 'Pending'; ?></div>
            <div class="text-sm text-gray-600">Credential Status</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-alt text-purple-600 text-xl"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900"><?php echo $currentYear; ?></div>
            <div class="text-sm text-gray-600">Tax Year</div>
        </div>
    </div>
    
    <!-- Subscription Alert -->
    <?php if (!$subscription || $subscription['status'] !== 'active'): ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mb-8">
            <div class="flex items-start">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold text-yellow-800">Subscription Required</h3>
                    <p class="text-yellow-700 mt-1">Activate your subscription to accept clients and access all features. Only $80/month with unlimited clients.</p>
                    <a href="/subscription" class="mt-3 inline-block bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition">
                        Activate Subscription
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Clients List -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold">Your Clients</h2>
            <a href="/clients" class="text-sm text-primary-600 hover:text-primary-700">Manage Clients</a>
        </div>
        
        <?php if (!empty($clients)): ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Client</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Type</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Year</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Status</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clients as $client): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 px-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                            <?php echo strtoupper(substr($client['first_name'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <div class="font-medium"><?php echo sanitize($client['first_name'] . ' ' . $client['last_name']); ?></div>
                                            <div class="text-sm text-gray-500"><?php echo sanitize($client['email']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="capitalize"><?php echo sanitize($client['role']); ?></span>
                                </td>
                                <td class="py-3 px-4"><?php echo $client['year']; ?></td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-1 bg-<?php echo $client['relationship_status'] === 'active' ? 'green' : 'yellow'; ?>-100 text-<?php echo $client['relationship_status'] === 'active' ? 'green' : 'yellow'; ?>-700 rounded-full text-sm">
                                        <?php echo ucfirst($client['relationship_status']); ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <a href="/clients?id=<?php echo $client['id']; ?>" class="text-primary-600 hover:text-primary-700">
                                        <i class="fas fa-eye mr-1"></i> View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Clients Yet</h3>
                <p class="text-gray-600 mb-4">Once healthcare professionals select you as their accountant, they'll appear here.</p>
                <?php if (!$user['is_verified']): ?>
                    <p class="text-sm text-yellow-600"><i class="fas fa-info-circle mr-1"></i> Get verified to be visible to potential clients</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <?php elseif (isAdmin()): ?>
    <!-- Admin Dashboard -->
    <div class="grid lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">
                <?php echo array_sum($stats['users_by_role'] ?? [0]); ?>
            </div>
            <div class="text-sm text-gray-600">Total Users</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-credit-card text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900"><?php echo $stats['active_subscriptions'] ?? 0; ?></div>
            <div class="text-sm text-gray-600">Active Subscriptions</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-handshake text-purple-600 text-xl"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900"><?php echo $stats['active_contracts'] ?? 0; ?></div>
            <div class="text-sm text-gray-600">Active Contracts</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-yellow-600 text-xl"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900"><?php echo formatCurrency($stats['total_commissions'] ?? 0); ?></div>
            <div class="text-sm text-gray-600">Total Commissions</div>
        </div>
    </div>
    
    <!-- Admin Quick Links -->
    <div class="grid lg:grid-cols-3 gap-6">
        <a href="/admin/users" class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
            <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center mb-4">
                <i class="fas fa-user-cog text-primary-600 text-xl"></i>
            </div>
            <h3 class="font-semibold text-lg mb-2">User Management</h3>
            <p class="text-gray-600 text-sm">Manage users, verify accountants, and handle accounts</p>
        </a>
        
        <a href="/admin/commissions" class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
            <div class="w-12 h-12 bg-accent-100 rounded-lg flex items-center justify-center mb-4">
                <i class="fas fa-percentage text-accent-600 text-xl"></i>
            </div>
            <h3 class="font-semibold text-lg mb-2">Commission Tracking</h3>
            <p class="text-gray-600 text-sm">View and manage platform commissions (12%)</p>
        </a>
        
        <a href="/admin" class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                <i class="fas fa-chart-bar text-purple-600 text-xl"></i>
            </div>
            <h3 class="font-semibold text-lg mb-2">Analytics</h3>
            <p class="text-gray-600 text-sm">View platform statistics and reports</p>
        </a>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
