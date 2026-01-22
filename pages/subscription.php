<?php
$pageTitle = 'Subscription';
requireAuth();

if (!isAccountant()) {
    header('Location: /dashboard');
    exit;
}

$user = getCurrentUser();
$subscription = getSubscriptionStatus($user['id']);

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav.php';
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Subscription Management</h1>
        <p class="text-gray-600 mt-1">Manage your MediTax Connect subscription</p>
    </div>
    
    <!-- Current Status -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
        <h2 class="text-lg font-semibold mb-4">Current Status</h2>
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-14 h-14 bg-<?php echo ($subscription && $subscription['status'] === 'active') ? 'green' : 'yellow'; ?>-100 rounded-full flex items-center justify-center mr-4">
                    <i class="fas fa-<?php echo ($subscription && $subscription['status'] === 'active') ? 'check-circle' : 'exclamation-circle'; ?> text-<?php echo ($subscription && $subscription['status'] === 'active') ? 'green' : 'yellow'; ?>-600 text-2xl"></i>
                </div>
                <div>
                    <div class="text-xl font-semibold text-gray-900">
                        <?php echo ($subscription && $subscription['status'] === 'active') ? 'Active Subscription' : 'No Active Subscription'; ?>
                    </div>
                    <?php if ($subscription && $subscription['status'] === 'active'): ?>
                        <p class="text-gray-600">Your subscription renews on <?php echo formatDate($subscription['end_date']); ?></p>
                    <?php else: ?>
                        <p class="text-gray-600">Activate your subscription to start accepting clients</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if ($subscription && $subscription['status'] === 'active'): ?>
                <span class="px-4 py-2 bg-green-100 text-green-700 rounded-full font-medium">
                    Active
                </span>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Subscription Plan -->
    <div class="bg-white rounded-xl shadow-lg border-2 border-primary-500 p-8">
        <div class="flex items-start justify-between mb-6">
            <div>
                <span class="inline-block px-3 py-1 bg-primary-100 text-primary-700 rounded-full text-sm font-medium mb-2">
                    Professional Plan
                </span>
                <h2 class="text-2xl font-bold text-gray-900">Accountant Subscription</h2>
            </div>
            <div class="text-right">
                <div class="text-4xl font-bold text-gray-900">$80</div>
                <div class="text-gray-600">/month</div>
            </div>
        </div>
        
        <div class="grid md:grid-cols-2 gap-6 mb-8">
            <div>
                <h3 class="font-semibold mb-3">What's Included:</h3>
                <ul class="space-y-2">
                    <li class="flex items-center text-gray-700">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        Unlimited client management
                    </li>
                    <li class="flex items-center text-gray-700">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        Full document access
                    </li>
                    <li class="flex items-center text-gray-700">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        Tax report generation tools
                    </li>
                    <li class="flex items-center text-gray-700">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        Secure messaging with clients
                    </li>
                    <li class="flex items-center text-gray-700">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        Verified badge on profile
                    </li>
                </ul>
            </div>
            <div>
                <h3 class="font-semibold mb-3">Platform Commission:</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-3xl font-bold text-gray-900 mb-1">12%</div>
                    <p class="text-gray-600 text-sm">Commission on each client contract. This helps maintain the platform and bring you quality healthcare clients.</p>
                </div>
            </div>
        </div>
        
        <?php if (!$subscription || $subscription['status'] !== 'active'): ?>
            <div class="border-t pt-6">
                <h3 class="font-semibold mb-4">Payment Details</h3>
                <form id="subscriptionForm" class="space-y-4">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Card Number</label>
                            <input type="text" placeholder="4242 4242 4242 4242" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Expiry</label>
                                <input type="text" placeholder="MM/YY" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">CVC</label>
                                <input type="text" placeholder="123" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 rounded-lg p-4 text-sm text-blue-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        This is a demo. Use any card details to simulate subscription activation.
                    </div>
                    
                    <button type="submit" class="w-full gradient-bg text-white py-4 rounded-lg font-semibold hover:opacity-90 transition text-lg">
                        <i class="fas fa-lock mr-2"></i>Subscribe for $80/month
                    </button>
                </form>
                
                <p class="text-center text-sm text-gray-500 mt-4">
                    <i class="fas fa-shield-alt mr-1"></i>
                    Secure payment. Cancel anytime. 14-day money-back guarantee.
                </p>
            </div>
        <?php else: ?>
            <div class="border-t pt-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600">Next billing date: <span class="font-medium"><?php echo formatDate($subscription['end_date']); ?></span></p>
                        <p class="text-gray-600">Amount: <span class="font-medium">$<?php echo number_format($subscription['amount'], 2); ?></span></p>
                    </div>
                    <button onclick="cancelSubscription()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        Cancel Subscription
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- FAQ -->
    <div class="mt-8 bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-semibold mb-4">Frequently Asked Questions</h2>
        <div class="space-y-4">
            <div class="border-b pb-4">
                <h3 class="font-medium text-gray-900">What happens after I subscribe?</h3>
                <p class="text-gray-600 mt-1">You'll immediately gain access to all features. Your profile will be visible to healthcare professionals looking for accountants.</p>
            </div>
            <div class="border-b pb-4">
                <h3 class="font-medium text-gray-900">How does the 12% commission work?</h3>
                <p class="text-gray-600 mt-1">When you complete a tax service contract with a client through the platform, 12% of the contract value is retained as a platform fee.</p>
            </div>
            <div>
                <h3 class="font-medium text-gray-900">Can I cancel anytime?</h3>
                <p class="text-gray-600 mt-1">Yes! You can cancel your subscription at any time. You'll continue to have access until the end of your billing period.</p>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('subscriptionForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    try {
        const response = await fetch('/api/subscription/activate', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({})
        });
        const data = await response.json();
        
        if (data.success) {
            alert('Subscription activated successfully!');
            window.location.reload();
        } else {
            alert(data.error || 'Activation failed');
        }
    } catch (error) {
        alert('Activation failed. Please try again.');
    }
});

function cancelSubscription() {
    if (confirm('Are you sure you want to cancel your subscription?')) {
        alert('Subscription cancellation simulated. In production, this would cancel your recurring payment.');
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
