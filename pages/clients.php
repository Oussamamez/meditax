<?php
$pageTitle = 'My Clients';
requireAuth();

if (!isAccountant()) {
    header('Location: /dashboard');
    exit;
}

$user = getCurrentUser();
$subscription = getSubscriptionStatus($user['id']);
$clients = getAccountantClients($user['id']);

// Get selected client details
$selectedClient = null;
$clientDocuments = [];
$clientFinancials = null;

if (isset($_GET['id'])) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
        SELECT u.*, ac.status as relationship_status, ac.year, ac.id as relationship_id
        FROM users u
        JOIN accountant_clients ac ON u.id = ac.client_id
        WHERE u.id = ? AND ac.accountant_id = ?
    ");
    $stmt->execute([$_GET['id'], $user['id']]);
    $selectedClient = $stmt->fetch();
    
    if ($selectedClient) {
        $clientDocuments = getUserDocuments($selectedClient['id'], $selectedClient['year']);
        $clientFinancials = getFinancialSummary($selectedClient['id'], $selectedClient['year']);
    }
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <?php if (!$subscription || $subscription['status'] !== 'active'): ?>
    <!-- Subscription Required Alert -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mb-8">
        <div class="flex items-start">
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
            </div>
            <div class="flex-1">
                <h3 class="font-semibold text-yellow-800">Active Subscription Required</h3>
                <p class="text-yellow-700 mt-1">You need an active subscription to manage clients and access their documents.</p>
                <a href="/subscription" class="mt-3 inline-block bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition">
                    Activate Subscription
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Clients List Sidebar -->
        <div class="lg:w-1/3">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Your Clients</h2>
                
                <?php if (!empty($clients)): ?>
                <div class="space-y-3">
                    <?php foreach ($clients as $client): ?>
                    <a href="/clients?id=<?php echo $client['id']; ?>" 
                       class="flex items-center p-3 rounded-lg transition <?php echo (isset($_GET['id']) && $_GET['id'] == $client['id']) ? 'bg-primary-50 border border-primary-200' : 'hover:bg-gray-50'; ?>">
                        <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-accent-500 rounded-full flex items-center justify-center text-white font-bold mr-3">
                            <?php echo strtoupper(substr($client['first_name'], 0, 1)); ?>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium"><?php echo sanitize($client['first_name'] . ' ' . $client['last_name']); ?></div>
                            <div class="text-sm text-gray-500">
                                <?php echo getRoleDisplayName($client['role']); ?> - <?php echo $client['year']; ?>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full <?php echo $client['relationship_status'] === 'active' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'; ?>">
                            <?php echo ucfirst($client['relationship_status']); ?>
                        </span>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-gray-400 text-2xl"></i>
                    </div>
                    <p class="text-gray-600">No clients yet</p>
                    <p class="text-sm text-gray-500 mt-1">Clients will appear here when they select you</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Client Details -->
        <div class="lg:w-2/3">
            <?php if ($selectedClient): ?>
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <div class="flex items-start justify-between mb-6">
                    <div class="flex items-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-accent-500 rounded-full flex items-center justify-center text-white text-xl font-bold mr-4">
                            <?php echo strtoupper(substr($selectedClient['first_name'], 0, 1) . substr($selectedClient['last_name'], 0, 1)); ?>
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold"><?php echo sanitize($selectedClient['first_name'] . ' ' . $selectedClient['last_name']); ?></h2>
                            <p class="text-gray-600"><?php echo getRoleDisplayName($selectedClient['role']); ?></p>
                            <?php if ($selectedClient['business_name']): ?>
                                <p class="text-sm text-gray-500"><?php echo sanitize($selectedClient['business_name']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if ($selectedClient['relationship_status'] === 'pending'): ?>
                    <button onclick="acceptClient(<?php echo $selectedClient['relationship_id']; ?>)" 
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-check mr-2"></i>Accept Client
                    </button>
                    <?php else: ?>
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full">Active</span>
                    <?php endif; ?>
                </div>
                
                <div class="grid md:grid-cols-2 gap-4 text-sm">
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-envelope w-5 text-gray-400"></i>
                        <span><?php echo sanitize($selectedClient['email']); ?></span>
                    </div>
                    <?php if ($selectedClient['phone']): ?>
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-phone w-5 text-gray-400"></i>
                        <span><?php echo sanitize($selectedClient['phone']); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($selectedClient['license_number']): ?>
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-id-card w-5 text-gray-400"></i>
                        <span>License: <?php echo sanitize($selectedClient['license_number']); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-calendar w-5 text-gray-400"></i>
                        <span>Tax Year: <?php echo $selectedClient['year']; ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Financial Summary -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">Financial Summary - <?php echo $selectedClient['year']; ?></h3>
                <div class="grid md:grid-cols-4 gap-4">
                    <div class="bg-green-50 rounded-lg p-4">
                        <div class="text-sm text-green-600 mb-1">Total Income</div>
                        <div class="text-xl font-bold text-green-700"><?php echo formatCurrency($clientFinancials['total_income']); ?></div>
                    </div>
                    <div class="bg-red-50 rounded-lg p-4">
                        <div class="text-sm text-red-600 mb-1">Total Expenses</div>
                        <div class="text-xl font-bold text-red-700"><?php echo formatCurrency($clientFinancials['total_expenses']); ?></div>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-4">
                        <div class="text-sm text-blue-600 mb-1">Profit</div>
                        <div class="text-xl font-bold text-blue-700"><?php echo formatCurrency($clientFinancials['estimated_profit']); ?></div>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-4">
                        <div class="text-sm text-purple-600 mb-1">Est. Tax</div>
                        <div class="text-xl font-bold text-purple-700"><?php echo formatCurrency($clientFinancials['estimated_tax']); ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Client Documents -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold mb-4">Client Documents</h3>
                
                <?php if (!empty($clientDocuments)): ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Document</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Category</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Uploaded</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clientDocuments as $doc): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 px-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-<?php echo $doc['category'] === 'income' ? 'green' : ($doc['category'] === 'expense' ? 'red' : 'blue'); ?>-100 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-file text-<?php echo $doc['category'] === 'income' ? 'green' : ($doc['category'] === 'expense' ? 'red' : 'blue'); ?>-600 text-sm"></i>
                                        </div>
                                        <span class="text-sm"><?php echo sanitize($doc['original_name']); ?></span>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-1 bg-gray-100 rounded text-xs capitalize"><?php echo $doc['category']; ?></span>
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-500"><?php echo formatDate($doc['uploaded_at']); ?></td>
                                <td class="py-3 px-4">
                                    <a href="/uploads/documents/<?php echo $doc['filename']; ?>" target="_blank" class="text-primary-600 hover:text-primary-700 text-sm">
                                        <i class="fas fa-download mr-1"></i>Download
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-folder-open text-3xl mb-2"></i>
                    <p>No documents uploaded yet</p>
                </div>
                <?php endif; ?>
            </div>
            
            <?php else: ?>
            <!-- No Client Selected -->
            <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-user text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Select a Client</h3>
                <p class="text-gray-600">Choose a client from the list to view their details and documents</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
async function acceptClient(relationshipId) {
    try {
        const response = await fetch('/api/client/accept', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({relationship_id: relationshipId})
        });
        const data = await response.json();
        
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.error || 'Failed to accept client');
        }
    } catch (error) {
        alert('Failed to accept client. Please try again.');
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
