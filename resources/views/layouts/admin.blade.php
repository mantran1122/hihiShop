<!DOCTYPE html>
<html lang="vi" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard') - HIHI Shop</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
</head>
<body x-data="{ sidebarOpen: false }" class="h-full font-sans antialiased text-slate-800 bg-slate-100 flex flex-col md:flex-row">

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm md:hidden" x-transition.opacity></div>

    <!-- Admin Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'" class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-950 text-slate-300 flex flex-col transition-transform duration-300 ease-in-out md:static md:translate-x-0 shrink-0 border-r border-slate-900">
        <!-- Sidebar Brand Header -->
        <div class="h-20 px-6 flex items-center justify-between border-b border-slate-900">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 text-xl font-black text-white">
                <span class="w-8 h-8 rounded-xl bg-gradient-to-tr from-rose-600 to-rose-400 text-white flex items-center justify-center font-black text-sm shadow-sm shadow-rose-500/30">
                    H
                </span>
                <span>HIHI<span class="text-rose-500">ADMIN</span></span>
            </a>
            <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-white p-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Sidebar Navigation Menu -->
        <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto no-scrollbar">
            <div class="px-3 pb-2 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                Menu Quản Trị
            </div>

            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.dashboard') || request()->routeIs('admin.index') ? 'bg-rose-600 text-white shadow-md shadow-rose-600/20' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                <span>Tổng quan</span>
            </a>

            <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.orders.*') ? 'bg-rose-600 text-white shadow-md shadow-rose-600/20' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                <span>Quản lý Đơn hàng</span>
            </a>

            <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.products.*') ? 'bg-rose-600 text-white shadow-md shadow-rose-600/20' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                <span>Quản lý Sản phẩm</span>
            </a>

            <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.users') ? 'bg-rose-600 text-white shadow-md shadow-rose-600/20' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span>Quản lý Người dùng</span>
            </a>

            <div class="pt-6 mt-6 border-t border-slate-900">
                <a href="{{ route('home') }}" target="_blank" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold text-sky-400 bg-sky-950/40 border border-sky-900/50 hover:bg-sky-900/50 transition">
                    <span>Xem Storefront</span>
                    <span>↗</span>
                </a>
            </div>
        </nav>

        <!-- Admin Profile footer -->
        <div class="p-4 border-t border-slate-900 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-full bg-slate-800 text-slate-300 font-bold text-xs flex items-center justify-center uppercase">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </span>
                <div class="text-xs">
                    <strong class="text-white block truncate max-w-[100px]">{{ Auth::user()->name }}</strong>
                    <span class="text-slate-500">Administrator</span>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="text-slate-500 hover:text-red-400 p-1.5 rounded-lg hover:bg-slate-900 transition" title="Đăng xuất">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <!-- Admin Topbar -->
        <header class="h-20 bg-white border-b border-slate-200/80 px-6 sm:px-8 flex items-center justify-between shrink-0 sticky top-0 z-30">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = true" class="md:hidden p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="text-lg sm:text-xl font-bold text-slate-900">
                    @yield('page_title', 'Bảng Quản Trị')
                </h1>
            </div>

            <div class="flex items-center gap-4">
                <span class="text-xs sm:text-sm text-slate-500 hidden sm:inline">
                    Đăng nhập dưới quyền: <strong class="text-slate-800">{{ Auth::user()->name }}</strong>
                </span>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="btn-danger btn-sm text-xs">
                        Đăng xuất
                    </button>
                </form>
            </div>
        </header>

        <!-- Admin Page Body -->
        <main class="flex-1 p-6 sm:p-8">
            <!-- Toast messages -->
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-emerald-800 flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-3">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <p class="text-sm font-semibold text-emerald-800">{{ session('success') }}</p>
                    </div>
                    <button @click="show = false" class="text-emerald-500 hover:text-emerald-700">✕</button>
                </div>
            @endif

            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200/80 text-rose-800 flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-3">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        <p class="text-sm font-semibold text-rose-800">{{ session('error') }}</p>
                    </div>
                    <button @click="show = false" class="text-rose-500 hover:text-rose-700">✕</button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

</body>
</html>
