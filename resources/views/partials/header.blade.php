<!-- resources/views/partials/header.blade.php -->
<header x-data="{ mobileMenuOpen: false }" class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200/80 transition-shadow duration-200 shadow-sm">
    <!-- Top Announcement Bar -->
    <div class="bg-slate-900 text-slate-300 text-xs py-2 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span class="font-medium text-slate-200">Miễn phí giao hàng COD toàn quốc cho tất cả đơn hàng hôm nay</span>
            </div>
            <div class="hidden sm:flex items-center gap-4 text-slate-300">
                <a href="tel:19000129" class="hover:text-white transition flex items-center gap-1.5 font-medium">
                    <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    Hotline: 1900 0129
                </a>
                <span>•</span>
                <span class="font-medium">Chính hãng 100%</span>
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 sm:h-20 gap-4">
            
            <!-- Mobile Menu Toggle Button -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="lg:hidden p-2 -ml-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-rose-500" aria-label="Menu">
                <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-show="mobileMenuOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-2xl font-black tracking-tight text-slate-900 group">
                <span class="w-9 h-9 rounded-xl bg-gradient-to-tr from-rose-600 to-rose-400 text-white flex items-center justify-center font-black text-lg shadow-sm shadow-rose-500/30 group-hover:scale-105 transition-transform duration-200">
                    H
                </span>
                <span>HIHI<span class="text-rose-600">Shop</span></span>
            </a>

            <!-- Search Bar (Desktop & Tablet) -->
            <div class="hidden md:flex flex-1 max-w-lg mx-6">
                <form action="{{ route('search') }}" method="GET" class="relative w-full">
                    <input 
                        type="search" 
                        name="q" 
                        value="{{ request('q') }}" 
                        placeholder="Tìm kiếm nước hoa, giày, mắt kính, đồng hồ..." 
                        class="w-full pl-11 pr-24 py-2.5 bg-slate-100/80 border border-transparent rounded-full text-sm text-slate-900 placeholder-slate-400 transition-all duration-200 focus:bg-white focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 focus:outline-none"
                    >
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <button type="submit" class="absolute inset-y-1 right-1 px-4 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-full transition shadow-sm cursor-pointer flex items-center">
                        Tìm kiếm
                    </button>
                </form>
            </div>

            <!-- Action Buttons & Account -->
            <div class="flex items-center gap-3 sm:gap-4">
                @auth
                    <!-- User Dropdown (Alpine) -->
                    <div x-data="{ open: false }" class="relative hidden sm:block">
                        <button @click="open = !open" @click.outside="open = false" class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-slate-100 transition text-sm font-semibold text-slate-700">
                            <span class="w-8 h-8 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-xs uppercase">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </span>
                            <span class="max-w-[120px] truncate text-slate-900">{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="open" x-cloak x-transition.opacity.scale.95 class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50">
                            @if(Auth::user()->hasRole('Admin'))
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                    Trang Quản Trị
                                </a>
                                <div class="border-t border-slate-100 my-1"></div>
                            @endif
                            <a href="{{ route('orders.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                Đơn mua của tôi
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition text-left cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Đăng xuất
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="hidden sm:flex items-center gap-2 text-sm">
                        <a href="{{ route('login') }}" class="px-3 py-1.5 text-slate-600 hover:text-slate-900 font-semibold transition">
                            Đăng nhập
                        </a>
                        <a href="{{ route('register') }}" class="px-3.5 py-1.5 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-xl font-bold transition">
                            Đăng ký
                        </a>
                    </div>
                @endauth

                <!-- Cart Button -->
                @auth
                    <a href="{{ route('cart.index') }}" class="relative inline-flex items-center gap-2.5 px-4 py-2 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-800 hover:text-rose-600 border border-slate-200/60 hover:border-rose-200 transition-all duration-200 font-bold text-sm shadow-sm">
                        <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <span class="hidden sm:inline">Giỏ hàng</span>
                        <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-xs font-black text-white bg-rose-600 rounded-full">
                            {{ $cartCount ?? 0 }}
                        </span>
                    </a>
                @else
                    <a href="{{ route('cart.index') }}" class="relative inline-flex items-center gap-2.5 px-4 py-2 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-800 hover:text-rose-600 border border-slate-200/60 hover:border-rose-200 transition-all duration-200 font-bold text-sm shadow-sm" title="Đăng nhập để xem giỏ hàng">
                        <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <span class="hidden sm:inline">Giỏ hàng</span>
                    </a>
                @endauth
            </div>

        </div>
    </div>

    <!-- Subnav: Category Pills with smooth horizontal scroll -->
    <nav class="border-t border-slate-100 bg-slate-50/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-2 py-2.5 overflow-x-auto no-scrollbar">
                <a href="{{ route('home') }}" class="px-3 py-1.5 rounded-lg text-sm font-semibold whitespace-nowrap transition-colors {{ request()->routeIs('home') ? 'bg-white text-rose-600 shadow-sm border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60' }}">
                    Trang chủ
                </a>

                @if(isset($headerCategories) && $headerCategories->count() > 0)
                    @foreach($headerCategories as $cat)
                        <a href="{{ route('category.show', $cat->slug) }}" class="px-3 py-1.5 rounded-lg text-sm font-semibold whitespace-nowrap transition-colors {{ request()->is('category/' . $cat->slug) ? 'bg-white text-rose-600 shadow-sm border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60' }}">
                            {{ $cat->name }}
                        </a>
                    @endforeach
                @endif

                <a href="{{ route('product.index') }}" class="px-3 py-1.5 rounded-lg text-sm font-semibold whitespace-nowrap transition-colors {{ request()->routeIs('product.index') ? 'bg-white text-rose-600 shadow-sm border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60' }}">
                    Tất cả sản phẩm
                </a>
            </div>
        </div>
    </nav>

    <!-- Mobile Drawer Menu (Alpine) -->
    <div x-show="mobileMenuOpen" x-cloak class="lg:hidden border-t border-slate-200 bg-white shadow-xl px-4 py-5 space-y-4">
        <!-- Mobile Search Form -->
        <form action="{{ route('search') }}" method="GET" class="relative">
            <input 
                type="search" 
                name="q" 
                value="{{ request('q') }}" 
                placeholder="Tìm sản phẩm..." 
                class="w-full pl-10 pr-4 py-2.5 bg-slate-100 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-rose-500 border border-transparent focus:border-rose-500"
            >
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </form>

        <!-- Mobile User Links -->
        <div class="pt-2 border-t border-slate-100">
            @auth
                <div class="flex items-center justify-between py-2">
                    <span class="text-sm font-semibold text-slate-800">Chào, <strong>{{ Auth::user()->name }}</strong></span>
                    @if(Auth::user()->hasRole('Admin'))
                        <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-white bg-rose-600 px-2 py-1 rounded">Admin</a>
                    @endif
                </div>
                <div class="grid grid-cols-2 gap-2 mt-2">
                    <a href="{{ route('orders.index') }}" class="btn-secondary text-xs py-2">Đơn mua của tôi</a>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="w-full btn-danger text-xs py-2">Đăng xuất</button>
                    </form>
                </div>
            @else
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('login') }}" class="btn-secondary text-xs py-2">Đăng nhập</a>
                    <a href="{{ route('register') }}" class="btn-primary text-xs py-2">Đăng ký</a>
                </div>
            @endauth
        </div>

        <!-- Hotline Call -->
        <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>Tư vấn khách hàng:</span>
            <a href="tel:19000129" class="font-bold text-rose-600">📞 1900 0129</a>
        </div>
    </div>
</header>
