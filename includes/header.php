<!DOCTYPE html>
<html lang="en" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'MediTax Connect'; ?> - Healthcare Tax Solutions</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        accent: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-bg { background: linear-gradient(135deg, #0284c7 0%, #0ea5e9 50%, #22c55e 100%); }
        .gradient-text { background: linear-gradient(135deg, #0284c7, #22c55e); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

        /* ── Dark mode overrides ──────────────────────────────────── */
        html.dark body { background-color: #0f172a; color: #e2e8f0; }

        html.dark .bg-white       { background-color: #1e293b !important; }
        html.dark .bg-gray-50     { background-color: #0f172a !important; }
        html.dark .bg-gray-100    { background-color: #1e293b !important; }
        html.dark .bg-gray-200    { background-color: #334155 !important; }

        html.dark .text-gray-900  { color: #f1f5f9 !important; }
        html.dark .text-gray-800  { color: #e2e8f0 !important; }
        html.dark .text-gray-700  { color: #cbd5e1 !important; }
        html.dark .text-gray-600  { color: #94a3b8 !important; }
        html.dark .text-gray-500  { color: #64748b !important; }
        html.dark .text-gray-400  { color: #475569 !important; }

        html.dark .border-gray-100 { border-color: #334155 !important; }
        html.dark .border-gray-200 { border-color: #334155 !important; }
        html.dark .border-b        { border-color: #334155 !important; }

        html.dark nav.bg-white     { background-color: #1e293b !important; }
        html.dark .shadow-sm       { box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.4) !important; }

        html.dark .hover\:bg-gray-50:hover   { background-color: #334155 !important; }
        html.dark .hover\:bg-gray-100:hover  { background-color: #334155 !important; }
        html.dark .hover\:bg-gray-200:hover  { background-color: #475569 !important; }

        html.dark table thead tr   { border-color: #334155 !important; }
        html.dark table tbody tr   { border-color: #334155 !important; }
        html.dark table tr.hover\:bg-gray-50:hover { background-color: #334155 !important; }

        html.dark .bg-yellow-50    { background-color: #1c1a00 !important; }
        html.dark .bg-green-100    { background-color: #052e16 !important; }
        html.dark .bg-red-100      { background-color: #2d0a0a !important; }
        html.dark .bg-blue-100     { background-color: #0c1a2e !important; }
        html.dark .bg-purple-100   { background-color: #1e0a2e !important; }
        html.dark .bg-primary-100  { background-color: #0c1a2e !important; }
        html.dark .bg-accent-100   { background-color: #052e16 !important; }

        /* Dark mode toggle button animation */
        #dark-toggle { transition: transform 0.2s ease; }
        #dark-toggle:hover { transform: rotate(15deg); }
    </style>
    <script>
        // Apply saved theme immediately to avoid flash
        (function() {
            const saved = localStorage.getItem('meditax-theme');
            if (saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
</head>
<body class="bg-gray-50 min-h-screen transition-colors duration-200">
<?php $flash = getFlashMessage(); ?>
<?php if ($flash): ?>
<div id="flash-message" class="fixed top-4 right-4 z-50 max-w-md">
    <div class="<?php echo $flash['type'] === 'success' ? 'bg-green-500' : ($flash['type'] === 'error' ? 'bg-red-500' : 'bg-blue-500'); ?> text-white px-6 py-4 rounded-lg shadow-lg flex items-center justify-between">
        <span><?php echo sanitize($flash['message']); ?></span>
        <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
<script>setTimeout(() => { const f = document.getElementById('flash-message'); if (f) f.remove(); }, 5000);</script>
<?php endif; ?>
