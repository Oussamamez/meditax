<?php
$pageTitle = 'Page Not Found';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav.php';
?>

<div class="min-h-[70vh] flex items-center justify-center px-4">
    <div class="text-center">
        <div class="w-24 h-24 gradient-bg rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-exclamation text-white text-4xl"></i>
        </div>
        <h1 class="text-6xl font-bold text-gray-900 mb-4">404</h1>
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Page Not Found</h2>
        <p class="text-gray-600 mb-8 max-w-md mx-auto">
            Sorry, the page you're looking for doesn't exist or has been moved.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/" class="gradient-bg text-white px-6 py-3 rounded-lg font-semibold hover:opacity-90 transition">
                <i class="fas fa-home mr-2"></i>Go Home
            </a>
            <a href="/dashboard" class="bg-gray-100 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-200 transition">
                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
