<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Bus Tracking System') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <!-- Navigation -->
    <nav class="bg-white dark:bg-gray-800 border-b-2 border-gray-300 dark:border-gray-700 shadow-lg">
        <div class="mx-auto px-6 lg:px-8">
            <div class="flex justify-between items-center h-24">
                <!-- Logo Centered -->
                <div class="flex-1 text-center">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        🚌 Admin Portal
                    </h1>
                </div>

                <!-- User Menu Right -->
                <div class="flex items-center space-x-6">
                    <!-- User Info -->
                    @if ($authUser)
                        <div class="text-right">
                            <p class="text-gray-900 dark:text-white font-semibold">
                                {{ $authUser['name'] ?? 'Admin' }}
                            </p>
                            <p class="text-gray-500 dark:text-gray-400 text-xs">
                                {{ strtoupper($authUser['role'] ?? 'admin') }}
                            </p>
                        </div>
                    @endif

                    <!-- Logout Button -->
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button
                            type="submit"
                            class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white px-4 py-2 rounded-md text-sm font-medium transition-colors"
                        >
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="mx-auto py-8 px-6 lg:px-8">
        <!-- Session Messages -->
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                <p class="text-green-800 dark:text-green-400">{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                <p class="text-red-800 dark:text-red-400">{{ session('error') }}</p>
            </div>
        @endif

        <!-- Page Content -->
        @yield('content')
    </div>
</body>
</html>
