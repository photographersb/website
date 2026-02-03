<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - Photographer SB</title>
    
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --burgundy-600: #8E0E3F;
            --burgundy-700: #6A0A30;
        }
        
        .bg-burgundy-600 { background-color: var(--burgundy-600); }
        .bg-burgundy-700 { background-color: var(--burgundy-700); }
        .text-burgundy-600 { color: var(--burgundy-600); }
        .border-burgundy-600 { border-color: var(--burgundy-600); }
        .hover\:bg-burgundy-700:hover { background-color: var(--burgundy-700); }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-burgundy-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div class="text-xl font-bold">Admin Panel</div>
                <div class="flex gap-6">
                    <a href="/admin/sitemap" class="hover:text-gray-200">Sitemap</a>
                    <a href="/" class="hover:text-gray-200">Back to Site</a>
                    @if (Route::has('logout'))
                        <form method="POST" action="/logout" style="display: inline;">
                            @csrf
                            <button type="submit" class="hover:text-gray-200">Logout</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-6">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white text-center py-4 mt-12">
        <p>&copy; 2026 Photographer SB. All rights reserved.</p>
    </footer>
</body>
</html>
