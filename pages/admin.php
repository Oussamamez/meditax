<?php
$pageTitle = 'Admin Panel';
requireAuth();
requireRole('admin');

$stats = getPlatformStats();
$recentUsers = getAllUsers();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
        <p class="text-gray-600 mt-1">Platform management and analytics</p>
    </div>
    
    <!-- Stats Grid -->
    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">
                <?php echo array_sum($stats['users_by_role'] ?? []); ?>
            </div>
            <div class="text-sm text-gray-600">Total Users</div>
            <div class="mt-2 text-xs text-gray-500">
                <?php 
                    $roles = $stats['users_by_role'] ?? [];
                    echo ($roles['doctor'] ?? 0) . ' doctors, ';
                    echo ($roles['dentist'] ?? 0) . ' dentists, ';
                    echo ($roles['pharmacy'] ?? 0) . ' pharmacies';
                ?>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calculator text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">
                <?php echo $stats['users_by_role']['accountant'] ?? 0; ?>
            </div>
            <div class="text-sm text-gray-600">Accountants</div>
            <div class="mt-2 text-xs text-gray-500">
                <?php echo $stats['active_subscriptions'] ?? 0; ?> with active subscriptions
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-handshake text-purple-600 text-xl"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">
                <?php echo $stats['active_contracts'] ?? 0; ?>
            </div>
            <div class="text-sm text-gray-600">Active Contracts</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-yellow-600 text-xl"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">
                <?php echo formatCurrency($stats['total_commissions'] ?? 0); ?>
            </div>
            <div class="text-sm text-gray-600">Total Commissions</div>
            <div class="mt-2 text-xs text-gray-500">12% platform fee</div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="grid md:grid-cols-3 gap-6 mb-8">
        <a href="/admin/users" class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
            <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center mb-4">
                <i class="fas fa-user-cog text-primary-600 text-xl"></i>
            </div>
            <h3 class="font-semibold text-lg mb-2">User Management</h3>
            <p class="text-gray-600 text-sm">Manage users, verify accountants, edit accounts</p>
        </a>
        
        <a href="/admin/commissions" class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
            <div class="w-12 h-12 bg-accent-100 rounded-lg flex items-center justify-center mb-4">
                <i class="fas fa-percentage text-accent-600 text-xl"></i>
            </div>
            <h3 class="font-semibold text-lg mb-2">Commission Tracking</h3>
            <p class="text-gray-600 text-sm">View and manage platform commissions</p>
        </a>
        
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                <i class="fas fa-chart-pie text-purple-600 text-xl"></i>
            </div>
            <h3 class="font-semibold text-lg mb-2">Revenue: $<?php echo number_format(($stats['active_subscriptions'] ?? 0) * 80, 2); ?>/mo</h3>
            <p class="text-gray-600 text-sm"><?php echo $stats['active_subscriptions'] ?? 0; ?> active subscriptions × $80</p>
        </div>
    </div>
    
    <!-- Recent Users -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold">Recent Users</h2>
            <a href="/admin/users" class="text-sm text-primary-600 hover:text-primary-700">View All</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">User</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Role</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Email</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Verified</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Joined</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($recentUsers, 0, 10) as $u): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                    <?php echo strtoupper(substr($u['first_name'], 0, 1)); ?>
                                </div>
                                <span class="font-medium"><?php echo sanitize($u['first_name'] . ' ' . $u['last_name']); ?></span>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 bg-gray-100 rounded text-xs capitalize"><?php echo $u['role']; ?></span>
                        </td>
                        <td class="py-3 px-4 text-sm text-gray-600"><?php echo sanitize($u['email']); ?></td>
                        <td class="py-3 px-4">
                            <?php if ($u['is_verified']): ?>
                                <span class="text-green-600"><i class="fas fa-check-circle"></i></span>
                            <?php else: ?>
                                <span class="text-gray-400"><i class="fas fa-times-circle"></i></span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4 text-sm text-gray-500"><?php echo formatDate($u['created_at']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
