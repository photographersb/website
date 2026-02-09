<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🛠️ Dev Tools - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @if(config('app.env') !== 'production' && config('app.debug') === true)
    <!-- Anti-caching meta tags (DEV MODE ONLY) -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    @endif
</head>
<body class="bg-gray-900 text-gray-100">
    <!-- DEBUG-VIEW: admin.dev-tools.index loaded -->
    
    <div class="min-h-screen">
        <!-- Header -->
        <div class="bg-gradient-to-r from-gray-800 to-gray-900 border-b border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                            🛠️ Developer Tools
                            <span class="text-sm font-normal px-3 py-1 bg-red-600 rounded-full">{{ strtoupper($info['env']) }}</span>
                        </h1>
                        <p class="text-gray-400 mt-1">System diagnostics and cache management</p>
                    </div>
                    <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg transition">
                        ← Back to Admin
                    </a>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Flash Messages -->
            @if(session('success'))
            <div class="mb-6 p-4 bg-green-900 border border-green-600 rounded-lg text-green-100">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 p-4 bg-red-900 border border-red-600 rounded-lg text-red-100">
                {{ session('error') }}
            </div>
            @endif

            @if(session('info'))
            <div class="mb-6 p-4 bg-blue-900 border border-blue-600 rounded-lg">
                <pre class="text-xs text-blue-100 overflow-x-auto">{{ session('info') }}</pre>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Cache Management -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                        <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                            🗑️ Cache Management
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Clear All Cache -->
                            <form action="{{ route('admin.dev.clear-cache') }}" method="POST" class="bg-gray-900 p-4 rounded-lg border border-gray-700">
                                @csrf
                                <h3 class="font-semibold text-white mb-2">Clear All Caches</h3>
                                <p class="text-sm text-gray-400 mb-3">Clears: optimize, cache, config, route, view</p>
                                <button type="submit" class="w-full px-4 py-2 bg-red-600 hover:bg-red-500 text-white font-semibold rounded-lg transition">
                                    🔥 Clear All
                                </button>
                            </form>

                            <!-- Clear View Cache -->
                            <form action="{{ route('admin.dev.clear-view-cache') }}" method="POST" class="bg-gray-900 p-4 rounded-lg border border-gray-700">
                                @csrf
                                <h3 class="font-semibold text-white mb-2">Clear View Cache</h3>
                                <p class="text-sm text-gray-400 mb-3">Clears compiled Blade templates</p>
                                <button type="submit" class="w-full px-4 py-2 bg-orange-600 hover:bg-orange-500 text-white font-semibold rounded-lg transition">
                                    👁️ Clear Views
                                </button>
                            </form>

                            <!-- Clear Config Cache -->
                            <form action="{{ route('admin.dev.clear-config-cache') }}" method="POST" class="bg-gray-900 p-4 rounded-lg border border-gray-700">
                                @csrf
                                <h3 class="font-semibold text-white mb-2">Clear Config Cache</h3>
                                <p class="text-sm text-gray-400 mb-3">Clears configuration cache</p>
                                <button type="submit" class="w-full px-4 py-2 bg-yellow-600 hover:bg-yellow-500 text-white font-semibold rounded-lg transition">
                                    ⚙️ Clear Config
                                </button>
                            </form>

                            <!-- Clear Route Cache -->
                            <form action="{{ route('admin.dev.clear-route-cache') }}" method="POST" class="bg-gray-900 p-4 rounded-lg border border-gray-700">
                                @csrf
                                <h3 class="font-semibold text-white mb-2">Clear Route Cache</h3>
                                <p class="text-sm text-gray-400 mb-3">Clears route cache</p>
                                <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-lg transition">
                                    🛣️ Clear Routes
                                </button>
                            </form>
                        </div>

                        <!-- Assets Info -->
                        <div class="mt-4">
                            <form action="{{ route('admin.dev.assets-info') }}" method="POST" class="bg-gray-900 p-4 rounded-lg border border-gray-700">
                                @csrf
                                <h3 class="font-semibold text-white mb-2">Assets Information</h3>
                                <p class="text-sm text-gray-400 mb-3">Check Vite build status</p>
                                <button type="submit" class="w-full px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white font-semibold rounded-lg transition">
                                    📦 Check Assets
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- System Info -->
                <div class="space-y-6">
                    <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                        <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                            📊 System Info
                        </h2>
                        
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between items-center pb-2 border-b border-gray-700">
                                <span class="text-gray-400">Environment:</span>
                                <span class="font-semibold text-white px-2 py-1 bg-red-900 rounded">{{ strtoupper($info['env']) }}</span>
                            </div>

                            <div class="flex justify-between items-center pb-2 border-b border-gray-700">
                                <span class="text-gray-400">Debug Mode:</span>
                                <span class="font-semibold {{ $info['debug'] ? 'text-red-400' : 'text-green-400' }}">
                                    {{ $info['debug'] ? '✅ ON' : '❌ OFF' }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center pb-2 border-b border-gray-700">
                                <span class="text-gray-400">Laravel:</span>
                                <span class="font-semibold text-blue-400">{{ $info['laravel_version'] }}</span>
                            </div>

                            <div class="flex justify-between items-center pb-2 border-b border-gray-700">
                                <span class="text-gray-400">PHP:</span>
                                <span class="font-semibold text-purple-400">{{ $info['php_version'] }}</span>
                            </div>

                            <div class="flex justify-between items-center pb-2 border-b border-gray-700">
                                <span class="text-gray-400">Git Commit:</span>
                                <span class="font-mono text-orange-400">{{ $info['git_commit'] }}</span>
                            </div>

                            <div class="flex justify-between items-center pb-2 border-b border-gray-700">
                                <span class="text-gray-400">Git Branch:</span>
                                <span class="font-mono text-cyan-400">{{ $info['git_branch'] }}</span>
                            </div>

                            <div class="flex justify-between items-center pb-2 border-b border-gray-700">
                                <span class="text-gray-400">Build Version:</span>
                                <span class="font-mono text-pink-400 text-xs">{{ Str::limit($info['build_version'], 20) }}</span>
                            </div>

                            <div class="flex justify-between items-center pb-2 border-b border-gray-700">
                                <span class="text-gray-400">Cache Driver:</span>
                                <span class="font-semibold text-yellow-400">{{ $info['cache_driver'] }}</span>
                            </div>

                            <div class="flex justify-between items-center pb-2 border-b border-gray-700">
                                <span class="text-gray-400">Vite Build:</span>
                                <span class="font-semibold {{ $info['vite_enabled'] ? 'text-green-400' : 'text-red-400' }}">
                                    {{ $info['vite_enabled'] ? '✅ Built' : '❌ Missing' }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-gray-400">Storage:</span>
                                <span class="font-semibold {{ $info['storage_writable'] ? 'text-green-400' : 'text-red-400' }}">
                                    {{ $info['storage_writable'] ? '✅ Writable' : '❌ Not Writable' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                        <h2 class="text-xl font-bold text-white mb-4">⚡ Quick Links</h2>
                        <div class="space-y-2">
                            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg transition text-center">
                                📊 Dashboard
                            </a>
                            <a href="{{ url('/') }}" target="_blank" class="block px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg transition text-center">
                                🌐 Frontend
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="block px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg transition text-center">
                                👥 Users
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
