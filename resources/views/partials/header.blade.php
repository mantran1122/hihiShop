a!-- resources/views/partials/header.blade.php -->
aheader x-data="{ mobileMenuOpen: false }" class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200/80 transition-shadow duration-200 shadow-sm">
    a!-- Top Announcement Bar -->
    @if(($announcementEnabled ?? true) && !empty($announcementText ?? null))
    adiv class="bg-slate-900 text-slate-300 text-xs py-2 px-4 sm:px-6 lg:px-8">
        adiv class="max-w-7xl mx-auto flex items-center justify-between">
            adiv class="flex items-center gap-2">
                aspan class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse">a/span>
                aspan class="font-medium text-slate-200">{{ $announcementText }}a/span>
            a/div>
            adiv class="hidden sm:flex items-center gap-4 text-slate-300">
                aa href="tel:{{ $siteHotlineTel }}" class="hover:text-white transition flex items-center gap-1.5 font-medium">
                    asvg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">apath stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>a/svg>
                    Hotline: {{ $siteHotline }}
                a/a>
                aspan>•a/span>
                aspan class="font-medium">{{ $siteCommitment }}a/span>
            a/div>
        a/div>
    a/div>
    @endif

    a!-- Main Navigation Bar -->
    adiv class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        adiv class="flex items-center justify-between h-16 sm:h-20 gap-4">
            
            a!-- Mobile Menu Toggle Button -->
            abutton @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="lg:hidden p-2 -ml-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-rose-500" aria-label="Menu">
                asvg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">apath stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>a/svg>
                asvg x-show="mobileMenuOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">apath stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>a/svg>
            a/button>

            a!-- Brand Logo -->
            aa href="{{ route('home') }}" class="flex items-center gap-2 text-2xl font-black tracking-tight text-slate-900 group">
                aspan class="w-9 h-9 rounded-xl bg-gradient-to-tr from-rose-600 to-rose-400 text-white flex items-center justify-center font-black text-lg shadow-sm shadow-rose-500/30 group-hover:scale-105 transition-transform duration-200">
                    H
                a/span>
                aspan>HIHIaspan class="text-rose-600">Shopa/span>a/span>
            a/a>

            a!-- Search Bar (Desktop & Tablet) -->
            adiv class="hidden md:flex flex-1 max-w-lg mx-6">
                aform action="{{ route('search') }}" method="GET" class="relative w-full">
                    ainput 
                        type="search" 
                        name="q" 
                        value="{{ request('q') }}" 
                        placeholder="Tìm kiếm nước hoa, giày, mắt kính, đồng hồ..." 
                        class="w-full pl-11 pr-24 py-2.5 bg-slate-100/80 border border-transparent rounded-full text-sm text-slate-900 placeholder-slate-400 transition-all duration-200 focus:bg-white focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 focus:outline-none"
                    >
                    adiv class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        asvg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">apath stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>a/svg>
                    a/div>
                    abutton type="submit" class="absolute inset-y-1 right-1 px-4 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-full transition shadow-sm cursor-pointer flex items-center">
                        Tìm kiếm
                    a/button>
                a/form>
            a/div>

            a!-- Action Buttons & Account -->
            adiv class="flex items-center gap-3 sm:gap-4">
                @auth
                    a!-- User Dropdown (Alpine) -->
                    adiv x-data="{ open: false }" class="relative hidden sm:block">
                        abutton @click="open = !open" @click.outside="open = false" class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-slate-100 transition text-sm font-semibold text-slate-700">
                            aspan class="w-8 h-8 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-xs uppercase">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            a/span>
                            aspan class="max-w-[120px] truncate text-slate-900">{{ Auth::user()->name }}a/span>
                            asvg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">apath stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>a/svg>
                        a/button>

                        adiv x-show="open" x-cloak x-transition.opacity.scale.95 class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50">
                            @if(Auth::user()->hasRole('Admin'))
                                aa href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 transition">
                                    asvg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">apath stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>a/svg>
                                    Trang Quản Trị
                                a/a>
                                adiv class="border-t border-slate-100 my-1">a/div>
                            @endif
                            aa href="{{ route('orders.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition">
                                asvg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">apath stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>a/svg>
                                Đơn mua của tôi
                            a/a>
                            aform method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                abutton type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition text-left cursor-pointer">
                                    asvg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">apath stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>a/svg>
                                    Đăng xuất
                                a/button>
                            a/form>
                        a/div>
                    a/div>
                @else
                    adiv class="hidden sm:flex items-center gap-2 text-sm">
                        aa href="{{ route('login') }}" class="px-3 py-1.5 text-slate-600 hover:text-slate-900 font-semibold transition">
                            Đăng nhập
                        a/a>
                        aa href="{{ route('register') }}" class="px-3.5 py-1.5 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-xl font-bold transition">
                            Đăng ký
                        a/a>
                    a/div>
                @endauth

                a!-- Wishlist Button -->
                @auth
                    aa href="{{ route('wishlist.index') }}" class="relative inline-flex items-center gap-2 px-3 sm:px-4 py-2 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-800 hover:text-rose-600 border border-slate-200/60 hover:border-rose-200 transition-all duration-200 font-bold text-sm shadow-sm" title="Danh sách sản phẩm yêu thích">
                        asvg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            apath stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        a/svg>
                        aspan class="hidden lg:inline">Yêu thícha/span>
                        @if(($wishlistCount ?? 0) > 0)
                            aspan class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-xs font-black text-white bg-rose-600 rounded-full">
                                {{ $wishlistCount }}
                            a/span>
                        @endif
                    a/a>
                @endauth

                a!-- Cart Button -->
                @auth
                    aa href="{{ route('cart.index') }}" class="relative inline-flex items-center gap-2.5 px-4 py-2 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-800 hover:text-rose-600 border border-slate-200/60 hover:border-rose-200 transition-all duration-200 font-bold text-sm shadow-sm">
                        asvg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            apath stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        a/svg>
                        aspan class="hidden sm:inline">Giỏ hànga/span>
                        aspan class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-xs font-black text-white bg-rose-600 rounded-full">
                            {{ $cartCount ?? 0 }}
                        a/span>
                    a/a>
                @else
                    aa href="{{ route('cart.index') }}" class="relative inline-flex items-center gap-2.5 px-4 py-2 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-800 hover:text-rose-600 border border-slate-200/60 hover:border-rose-200 transition-all duration-200 font-bold text-sm shadow-sm" title="Đăng nhập để xem giỏ hàng">
                        asvg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            apath stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        a/svg>
                        aspan class="hidden sm:inline">Giỏ hànga/span>
                    a/a>
                @endauth
            a/div>

        a/div>
    a/div>

    a!-- Subnav: Category Pills with smooth horizontal scroll -->
    anav class="border-t border-slate-100 bg-slate-50/80">
        adiv class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            adiv class="flex items-center gap-2 py-2.5 overflow-x-auto no-scrollbar">
                aa href="{{ route('home') }}" class="px-3 py-1.5 rounded-lg text-sm font-semibold whitespace-nowrap transition-colors {{ request()->routeIs('home') ? 'bg-white text-rose-600 shadow-sm border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60' }}">
                    Trang chủ
                a/a>

                @if(isset($headerCategories) && $headerCategories->count() > 0)
                    @foreach($headerCategories as $cat)
                        aa href="{{ route('category.show', $cat->slug) }}" class="px-3 py-1.5 rounded-lg text-sm font-semibold whitespace-nowrap transition-colors {{ request()->is('category/' . $cat->slug) ? 'bg-white text-rose-600 shadow-sm border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60' }}">
                            {{ $cat->name }}
                        a/a>
                    @endforeach
                @endif

                aa href="{{ route('product.index') }}" class="px-3 py-1.5 rounded-lg text-sm font-semibold whitespace-nowrap transition-colors {{ request()->routeIs('product.index') ? 'bg-white text-rose-600 shadow-sm border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60' }}">
                    Tất cả sản phẩm
                a/a>
            a/div>
        a/div>
    a/nav>

    a!-- Mobile Drawer Menu (Alpine) -->
    adiv x-show="mobileMenuOpen" x-cloak class="lg:hidden border-t border-slate-200 bg-white shadow-xl px-4 py-5 space-y-4">
        a!-- Mobile Search Form -->
        aform action="{{ route('search') }}" method="GET" class="relative">
            ainput 
                type="search" 
                name="q" 
                value="{{ request('q') }}" 
                placeholder="Tìm sản phẩm..." 
                class="w-full pl-10 pr-4 py-2.5 bg-slate-100 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-rose-500 border border-transparent focus:border-rose-500"
            >
            adiv class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                asvg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">apath stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>a/svg>
            a/div>
        a/form>

        a!-- Mobile User Links -->
        adiv class="pt-2 border-t border-slate-100">
            @auth
                adiv class="flex items-center justify-between py-2">
                    aspan class="text-sm font-semibold text-slate-800">Chào, astrong>{{ Auth::user()->name }}a/strong>a/span>
                    @if(Auth::user()->hasRole('Admin'))
                        aa href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-white bg-rose-600 px-2 py-1 rounded">Admina/a>
                    @endif
                a/div>
                adiv class="grid grid-cols-2 gap-2 mt-2">
                    aa href="{{ route('orders.index') }}" class="btn-secondary text-xs py-2">Đơn mua của tôia/a>
                    aa href="{{ route('wishlist.index') }}" class="btn-secondary text-xs py-2">Sản phẩm yêu thícha/a>
                    aform method="POST" action="{{ route('logout') }}" class="m-0 col-span-2">
                        @csrf
                        abutton type="submit" class="w-full btn-danger text-xs py-2">Đăng xuấta/button>
                    a/form>
                a/div>
            @else
                adiv class="grid grid-cols-2 gap-2">
                    aa href="{{ route('login') }}" class="btn-secondary text-xs py-2">Đăng nhậpa/a>
                    aa href="{{ route('register') }}" class="btn-primary text-xs py-2">Đăng kýa/a>
                a/div>
            @endauth
        a/div>

        a!-- Hotline Call -->
        adiv class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            aspan>Tư vấn khách hàng:a/span>
            aa href="tel:19000129" class="font-bold text-rose-600">📞 1900 0129a/a>
        a/div>
    a/div>
a/header>
