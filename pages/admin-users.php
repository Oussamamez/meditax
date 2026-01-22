<?php
$pageTitle = 'User Management';
requireAuth();
requireRole('admin');

$roleFilter = $_GET['role'] ?? null;
$users = getAllUsers($roleFilter);

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">User Management</h1>
            <p class="text-gray-600 mt-1">Manage all platform users</p>
        </div>
        <div class="mt-4 md:mt-0 flex items-center space-x-4">
            <select id="roleFilter" onchange="window.location.href='/admin/users?role='+this.value" 
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                <option value="">All Roles</option>
                <option value="doctor" <?php echo $roleFilter === 'doctor' ? 'selected' : ''; ?>>Doctors</option>
                <option value="dentist" <?php echo $roleFilter === 'dentist' ? 'selected' : ''; ?>>Dentists</option>
                <option value="pharmacy" <?php echo $roleFilter === 'pharmacy' ? 'selected' : ''; ?>>Pharmacies</option>
                <option value="accountant" <?php echo $roleFilter === 'accountant' ? 'selected' : ''; ?>>Accountants</option>
            </select>
        </div>
    </div>
    
    <!-- Users Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-600">User</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-600">Role</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-600">Business</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-600">Verified</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-600">Joined</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-4 px-6">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-accent-500 rounded-full flex items-center justify-center text-white font-bold mr-3">
                                    <?php echo strtoupper(substr($u['first_name'], 0, 1)); ?>
                                </div>
                                <div>
                                    <div class="font-medium"><?php echo sanitize($u['first_name'] . ' ' . $u['last_name']); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo sanitize($u['email']); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <span class="px-3 py-1 bg-<?php echo $u['role'] === 'accountant' ? 'green' : 'blue'; ?>-100 text-<?php echo $u['role'] === 'accountant' ? 'green' : 'blue'; ?>-700 rounded-full text-sm capitalize">
                                <?php echo $u['role']; ?>
                            </span>
                        </td>
                        <td class="py-4 px-6 text-sm text-gray-600">
                            <?php echo sanitize($u['business_name'] ?? '-'); ?>
                        </td>
                        <td class="py-4 px-6">
                            <?php if ($u['is_verified']): ?>
                                <span class="inline-flex items-center text-green-600">
                                    <i class="fas fa-check-circle mr-1"></i> Verified
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center text-gray-400">
                                    <i class="fas fa-clock mr-1"></i> Pending
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="py-4 px-6 text-sm text-gray-500">
                            <?php echo formatDate($u['created_at']); ?>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center space-x-2">
                                <?php if (!$u['is_verified'] && $u['role'] === 'accountant'): ?>
                                <button onclick="verifyUser(<?php echo $u['id']; ?>)" 
                                        class="px-3 py-1 bg-green-600 text-white rounded text-sm hover:bg-green-700 transition">
                                    Verify
                                </button>
                                <?php endif; ?>
                                <button onclick="editUser(<?php echo $u['id']; ?>)" 
                                        class="px-3 py-1 bg-gray-100 text-gray-700 rounded text-sm hover:bg-gray-200 transition">
                                    Edit
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (empty($users)): ?>
        <div class="text-center py-12">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-users text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Users Found</h3>
            <p class="text-gray-600">No users match the selected filter.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
async function verifyUser(userId) {
    if (!confirm('Are you sure you want to verify this accountant?')) return;
    
    try {
        const response = await fetch('/api/admin/verify-user', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({user_id: userId})
        });
        const data = await response.json();
        
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.error || 'Verification failed');
        }
    } catch (error) {
        alert('Verification failed. Please try again.');
    }
}

function editUser(userId) {
    alert('Edit user functionality - In production, this would open an edit modal.');
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
