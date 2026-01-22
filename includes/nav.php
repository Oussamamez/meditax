<nav class="bg-white shadow-sm border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="/" class="flex items-center space-x-2">
                    <div class="w-10 h-10 gradient-bg rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-medical text-white text-lg"></i>
                    </div>
                    <span class="text-xl font-bold gradient-text">MediTax Connect</span>
                </a>
            </div>
            
            <div class="hidden md:flex items-center space-x-8">
                <?php if (!isLoggedIn()): ?>
                    <a href="/#how-it-works" class="text-gray-600 hover:text-primary-600 transition">How It Works</a>
                    <a href="/#pricing" class="text-gray-600 hover:text-primary-600 transition">Pricing</a>
                    <a href="/#benefits" class="text-gray-600 hover:text-primary-600 transition">Benefits</a>
                    <a href="/login" class="text-gray-600 hover:text-primary-600 transition">Login</a>
                    <a href="/register" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition">Get Started</a>
                <?php else: ?>
                    <a href="/dashboard" class="text-gray-600 hover:text-primary-600 transition">Dashboard</a>
                    <?php if (isHealthcarePro()): ?>
                        <a href="/documents" class="text-gray-600 hover:text-primary-600 transition">Documents</a>
                        <a href="/accountants" class="text-gray-600 hover:text-primary-600 transition">Find Accountant</a>
                    <?php elseif (isAccountant()): ?>
                        <a href="/clients" class="text-gray-600 hover:text-primary-600 transition">Clients</a>
                        <a href="/subscription" class="text-gray-600 hover:text-primary-600 transition">Subscription</a>
                    <?php elseif (isAdmin()): ?>
                        <a href="/admin" class="text-gray-600 hover:text-primary-600 transition">Admin Panel</a>
                    <?php endif; ?>
                    <a href="/profile" class="text-gray-600 hover:text-primary-600 transition">Profile</a>
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-gray-500">
                            <i class="fas fa-user-circle mr-1"></i>
                            <?php echo sanitize($_SESSION['user_name'] ?? 'User'); ?>
                        </span>
                        <a href="/logout" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition">Logout</a>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Mobile menu button -->
            <div class="md:hidden flex items-center">
                <button onclick="toggleMobileMenu()" class="text-gray-600 hover:text-primary-600">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Mobile menu -->
    <div id="mobile-menu" class="hidden md:hidden bg-white border-t">
        <div class="px-4 py-3 space-y-2">
            <?php if (!isLoggedIn()): ?>
                <a href="/#how-it-works" class="block py-2 text-gray-600">How It Works</a>
                <a href="/#pricing" class="block py-2 text-gray-600">Pricing</a>
                <a href="/login" class="block py-2 text-gray-600">Login</a>
                <a href="/register" class="block py-2 text-primary-600 font-medium">Get Started</a>
            <?php else: ?>
                <a href="/dashboard" class="block py-2 text-gray-600">Dashboard</a>
                <a href="/profile" class="block py-2 text-gray-600">Profile</a>
                <a href="/logout" class="block py-2 text-gray-600">Logout</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<script>
function toggleMobileMenu() {
    document.getElementById('mobile-menu').classList.toggle('hidden');
}
</script>
