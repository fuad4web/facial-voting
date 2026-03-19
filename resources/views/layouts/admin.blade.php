<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link rel="icon" type="image/svg+xml" href="{{ asset('download.svg') }}">

    <title>VeriVote - Secure Voting System</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="w-64 bg-indigo-800 text-white flex flex-col">
            <div class="p-4">
                <h1 class="text-xl font-bold">Admin Panel</h1>
                <p class="text-sm text-indigo-200 mt-1">Secure Voting System</p>
            </div>
            <nav class="flex-1 px-2 py-4 space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="block py-2.5 px-4 rounded hover:bg-indigo-700 {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-700' : '' }}">
                    Dashboard
                </a>
                <a href="{{ route('admin.categories.index') }}" class="block py-2.5 px-4 rounded hover:bg-indigo-700 {{ request()->routeIs('admin.categories.*') ? 'bg-indigo-700' : '' }}">
                    Categories
                </a>
                <a href="{{ route('admin.candidates.index') }}" class="block py-2.5 px-4 rounded hover:bg-indigo-700 {{ request()->routeIs('admin.candidates.*') ? 'bg-indigo-700' : '' }}">
                    Candidates
                </a>
                <a href="{{ route('admin.votes.index') }}" class="block py-2.5 px-4 rounded hover:bg-indigo-700 {{ request()->routeIs('admin.votes.*') ? 'bg-indigo-700' : '' }}">
                    Votes & Results
                </a>
                <a href="{{ route('dashboard') }}" class="block py-2.5 px-4 rounded hover:bg-indigo-700 mt-8 border-t border-indigo-700 pt-4">
                    ← Back to Site
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <header class="bg-white shadow">
                <div class="px-6 py-4 flex justify-between items-center">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        @yield('header')
                    </h2>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600">{{ Auth::user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm text-gray-600 hover:text-gray-900">Logout</button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-6">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
