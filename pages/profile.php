<?php
$pageTitle = 'Profile';
requireAuth();

$user = getCurrentUser();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("
        UPDATE users SET 
            first_name = ?,
            last_name = ?,
            phone = ?,
            address = ?,
            business_name = ?,
            license_number = ?,
            specialty = ?,
            updated_at = CURRENT_TIMESTAMP
        WHERE id = ?
    ");
    
    $stmt->execute([
        sanitize($_POST['first_name']),
        sanitize($_POST['last_name']),
        sanitize($_POST['phone']),
        sanitize($_POST['address']),
        sanitize($_POST['business_name']),
        sanitize($_POST['license_number']),
        sanitize($_POST['specialty']),
        $user['id']
    ]);
    
    setFlashMessage('success', 'Profile updated successfully!');
    header('Location: /profile');
    exit;
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav.php';
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Profile Settings</h1>
        <p class="text-gray-600 mt-1">Manage your account information</p>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form method="POST" action="/profile" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            
            <!-- Profile Header -->
            <div class="flex items-center mb-6 pb-6 border-b">
                <div class="w-20 h-20 bg-gradient-to-br from-primary-500 to-accent-500 rounded-full flex items-center justify-center text-white text-2xl font-bold mr-6">
                    <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
                </div>
                <div>
                    <h2 class="text-xl font-semibold"><?php echo sanitize($user['first_name'] . ' ' . $user['last_name']); ?></h2>
                    <p class="text-gray-600"><?php echo sanitize($user['email']); ?></p>
                    <span class="inline-flex items-center mt-2 px-3 py-1 bg-primary-100 text-primary-700 rounded-full text-sm">
                        <i class="fas fa-<?php echo isHealthcarePro() ? 'user-md' : (isAccountant() ? 'calculator' : 'shield-alt'); ?> mr-1"></i>
                        <?php echo getRoleDisplayName($user['role']); ?>
                    </span>
                </div>
            </div>
            
            <!-- Personal Info -->
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                    <input type="text" id="first_name" name="first_name" required 
                           value="<?php echo sanitize($user['first_name']); ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                </div>
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                    <input type="text" id="last_name" name="last_name" required 
                           value="<?php echo sanitize($user['last_name']); ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                </div>
            </div>
            
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                <input type="email" id="email" disabled
                       value="<?php echo sanitize($user['email']); ?>"
                       class="w-full px-4 py-3 border border-gray-200 rounded-lg bg-gray-50 text-gray-500">
                <p class="text-sm text-gray-500 mt-1">Contact support to change your email</p>
            </div>
            
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                <input type="tel" id="phone" name="phone" 
                       value="<?php echo sanitize($user['phone'] ?? ''); ?>"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                       placeholder="+1 (555) 123-4567">
            </div>
            
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                <textarea id="address" name="address" rows="2"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                          placeholder="Your business address"><?php echo sanitize($user['address'] ?? ''); ?></textarea>
            </div>
            
            <!-- Business Info -->
            <div class="pt-6 border-t">
                <h3 class="text-lg font-semibold mb-4">Business Information</h3>
                
                <div class="space-y-4">
                    <div>
                        <label for="business_name" class="block text-sm font-medium text-gray-700 mb-2">Business/Practice Name</label>
                        <input type="text" id="business_name" name="business_name" 
                               value="<?php echo sanitize($user['business_name'] ?? ''); ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                    </div>
                    
                    <div>
                        <label for="license_number" class="block text-sm font-medium text-gray-700 mb-2">License Number</label>
                        <input type="text" id="license_number" name="license_number" 
                               value="<?php echo sanitize($user['license_number'] ?? ''); ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                    </div>
                    
                    <?php if (!isAccountant()): ?>
                    <div>
                        <label for="specialty" class="block text-sm font-medium text-gray-700 mb-2">Specialty</label>
                        <input type="text" id="specialty" name="specialty" 
                               value="<?php echo sanitize($user['specialty'] ?? ''); ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                    </div>
                    <?php else: ?>
                    <input type="hidden" name="specialty" value="<?php echo sanitize($user['specialty'] ?? ''); ?>">
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Verification Status -->
            <?php if (isAccountant()): ?>
            <div class="pt-6 border-t">
                <h3 class="text-lg font-semibold mb-4">Verification Status</h3>
                <div class="flex items-center p-4 bg-<?php echo $user['is_verified'] ? 'green' : 'yellow'; ?>-50 rounded-lg">
                    <div class="w-12 h-12 bg-<?php echo $user['is_verified'] ? 'green' : 'yellow'; ?>-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-<?php echo $user['is_verified'] ? 'check-circle' : 'clock'; ?> text-<?php echo $user['is_verified'] ? 'green' : 'yellow'; ?>-600 text-xl"></i>
                    </div>
                    <div>
                        <div class="font-medium text-<?php echo $user['is_verified'] ? 'green' : 'yellow'; ?>-800">
                            <?php echo $user['is_verified'] ? 'Verified Accountant' : 'Verification Pending'; ?>
                        </div>
                        <p class="text-sm text-<?php echo $user['is_verified'] ? 'green' : 'yellow'; ?>-700">
                            <?php echo $user['is_verified'] ? 'Your credentials have been verified. You can accept clients.' : 'Our team is reviewing your credentials. This usually takes 1-2 business days.'; ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="pt-6 border-t">
                <button type="submit" class="gradient-bg text-white px-6 py-3 rounded-lg font-semibold hover:opacity-90 transition">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
