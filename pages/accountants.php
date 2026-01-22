<?php
$pageTitle = 'Find an Accountant';
requireAuth();

if (!isHealthcarePro()) {
    header('Location: /dashboard');
    exit;
}

$user = getCurrentUser();
$currentYear = date('Y');
$accountants = getVerifiedAccountants();
$currentAccountant = getClientAccountant($user['id'], $currentYear);

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Find an Accountant</h1>
        <p class="text-gray-600 mt-1">Browse verified accountants specializing in healthcare tax management</p>
    </div>
    
    <?php if ($currentAccountant): ?>
    <!-- Current Accountant -->
    <div class="bg-green-50 border border-green-200 rounded-xl p-6 mb-8">
        <div class="flex items-start">
            <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-accent-500 rounded-full flex items-center justify-center text-white text-xl font-bold mr-4 flex-shrink-0">
                <?php echo strtoupper(substr($currentAccountant['first_name'], 0, 1) . substr($currentAccountant['last_name'], 0, 1)); ?>
            </div>
            <div class="flex-1">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-green-800">Your Accountant for <?php echo $currentYear; ?></h3>
                        <p class="text-green-700 font-medium"><?php echo sanitize($currentAccountant['first_name'] . ' ' . $currentAccountant['last_name']); ?></p>
                    </div>
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">
                        <?php echo ucfirst($currentAccountant['relationship_status']); ?>
                    </span>
                </div>
                <div class="mt-2 text-sm text-green-600">
                    <span class="mr-4"><i class="fas fa-envelope mr-1"></i> <?php echo sanitize($currentAccountant['email']); ?></span>
                    <?php if ($currentAccountant['phone']): ?>
                        <span><i class="fas fa-phone mr-1"></i> <?php echo sanitize($currentAccountant['phone']); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Accountants Grid -->
    <?php if (!empty($accountants)): ?>
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($accountants as $accountant): ?>
        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition <?php echo ($currentAccountant && $currentAccountant['id'] == $accountant['id']) ? 'ring-2 ring-primary-500' : ''; ?>">
            <div class="flex items-start mb-4">
                <div class="w-14 h-14 bg-gradient-to-br from-primary-500 to-accent-500 rounded-full flex items-center justify-center text-white text-lg font-bold mr-4">
                    <?php echo strtoupper(substr($accountant['first_name'], 0, 1) . substr($accountant['last_name'], 0, 1)); ?>
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold text-lg"><?php echo sanitize($accountant['first_name'] . ' ' . $accountant['last_name']); ?></h3>
                    <p class="text-gray-600 text-sm"><?php echo sanitize($accountant['business_name'] ?? 'Independent Accountant'); ?></p>
                    <?php if ($accountant['is_verified']): ?>
                        <span class="inline-flex items-center text-xs text-green-600 mt-1">
                            <i class="fas fa-check-circle mr-1"></i> Verified
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="space-y-2 mb-4 text-sm text-gray-600">
                <?php if ($accountant['license_number']): ?>
                    <div class="flex items-center">
                        <i class="fas fa-id-card w-5 text-gray-400"></i>
                        <span>License: <?php echo sanitize($accountant['license_number']); ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($accountant['specialty']): ?>
                    <div class="flex items-center">
                        <i class="fas fa-briefcase w-5 text-gray-400"></i>
                        <span><?php echo sanitize($accountant['specialty']); ?></span>
                    </div>
                <?php endif; ?>
                <div class="flex items-center">
                    <i class="fas fa-envelope w-5 text-gray-400"></i>
                    <span><?php echo sanitize($accountant['email']); ?></span>
                </div>
            </div>
            
            <?php if ($currentAccountant && $currentAccountant['id'] == $accountant['id']): ?>
                <button disabled class="w-full py-3 bg-gray-100 text-gray-500 rounded-lg">
                    <i class="fas fa-check mr-2"></i>Currently Selected
                </button>
            <?php else: ?>
                <button onclick="selectAccountant(<?php echo $accountant['id']; ?>, '<?php echo sanitize($accountant['first_name'] . ' ' . $accountant['last_name']); ?>')" 
                        class="w-full py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition font-medium">
                    <i class="fas fa-user-plus mr-2"></i>Select Accountant
                </button>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-xl shadow-sm p-12 text-center">
        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-user-slash text-gray-400 text-3xl"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">No Verified Accountants Available</h3>
        <p class="text-gray-600 max-w-md mx-auto">
            We're currently verifying accountants on our platform. Please check back soon or contact support for assistance.
        </p>
    </div>
    <?php endif; ?>
</div>

<!-- Confirmation Modal -->
<div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-user-check text-primary-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-semibold">Confirm Selection</h3>
            <p class="text-gray-600 mt-2">
                You are about to select <span id="accountantName" class="font-medium text-gray-900"></span> as your accountant for <?php echo $currentYear; ?>.
            </p>
        </div>
        
        <div class="bg-gray-50 rounded-lg p-4 mb-6 text-sm text-gray-600">
            <p><i class="fas fa-info-circle text-primary-500 mr-2"></i>The accountant will be notified and can then access your uploaded documents.</p>
        </div>
        
        <div class="flex space-x-3">
            <button onclick="closeConfirmModal()" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                Cancel
            </button>
            <button onclick="confirmSelection()" class="flex-1 gradient-bg text-white px-4 py-3 rounded-lg font-medium hover:opacity-90 transition">
                Confirm
            </button>
        </div>
    </div>
</div>

<script>
let selectedAccountantId = null;

function selectAccountant(id, name) {
    selectedAccountantId = id;
    document.getElementById('accountantName').textContent = name;
    document.getElementById('confirmModal').classList.remove('hidden');
}

function closeConfirmModal() {
    document.getElementById('confirmModal').classList.add('hidden');
    selectedAccountantId = null;
}

async function confirmSelection() {
    if (!selectedAccountantId) return;
    
    try {
        const response = await fetch('/api/accountant/select', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                accountant_id: selectedAccountantId,
                year: <?php echo $currentYear; ?>
            })
        });
        const data = await response.json();
        
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.error || 'Selection failed');
            closeConfirmModal();
        }
    } catch (error) {
        alert('Selection failed. Please try again.');
        closeConfirmModal();
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
