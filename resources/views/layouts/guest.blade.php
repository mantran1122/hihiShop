@props([
    'raw' => false,
    'bodyClass' => 'bg-slate-950 text-slate-100',
    'maxWidth' => 'max-w-md',
    'title' => null,
])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? (config('app.name', 'HIHI Shop') . ' - Đăng Nhập & Đăng Ký') }}</title>

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased {{ $bodyClass }} min-h-screen flex flex-col justify-between selection:bg-rose-500/25 selection:text-rose-200 transition-colors duration-300 relative overflow-x-hidden">

        <!-- Ambient Glow Effects matching Hero banner -->
        <div class="fixed top-0 left-1/4 w-96 h-96 bg-rose-600/10 rounded-full blur-3xl pointer-events-none -z-10"></div>
        <div class="fixed bottom-0 right-1/4 w-96 h-96 bg-rose-900/15 rounded-full blur-3xl pointer-events-none -z-10"></div>

        <!-- Top Navigation -->
        <header class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex items-center justify-between z-10">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-400 hover:text-rose-400 transition group">
                <span class="w-8 h-8 rounded-xl bg-slate-900 border border-slate-800 shadow-sm flex items-center justify-center group-hover:border-rose-500/50 group-hover:bg-slate-800 transition">
                    ←
                </span>
                <span>Về trang chủ</span>
            </a>

            <a href="{{ route('home') }}" class="font-black text-xl text-white tracking-tight flex items-center gap-1.5">
                <span class="w-7 h-7 rounded-xl bg-gradient-to-tr from-rose-600 to-rose-400 text-white flex items-center justify-center font-black text-xs shadow-md shadow-rose-600/30">H</span>
                <span>HIHI<span class="text-rose-500">Shop</span></span>
            </a>

            <div class="flex items-center gap-2 text-xs text-slate-400 font-medium">
                <span class="hidden sm:inline">Hỗ trợ 24/7:</span>
                <a href="tel:19000129" class="font-bold text-rose-400 hover:underline">1900 0129</a>
            </div>
        </header>

        @if($raw)
            <main class="w-full flex-1 flex flex-col items-center justify-center py-4 sm:py-6 px-3 sm:px-4 z-10">
                {{ $slot }}
            </main>
        @else
            <!-- Main Auth Container for simple pages (Forgot password, Reset password) -->
            <main class="w-full {{ $maxWidth }} mx-auto px-4 py-8 flex flex-col items-center z-10">
                <!-- Brand Logo -->
                <div class="mb-6 text-center">
                    <a href="{{ route('home') }}" class="inline-block transform hover:scale-105 transition-transform duration-200">
                        <x-application-logo />
                    </a>
                </div>

                <!-- Card Content Box -->
                <div class="w-full bg-slate-900 rounded-3xl border border-slate-800 shadow-2xl p-6 sm:p-8">
                    {{ $slot }}
                </div>
            </main>
        @endif

        <!-- Footer -->
        <footer class="w-full max-w-7xl mx-auto px-4 py-4 text-center text-xs text-slate-500 z-10">
            <p>&copy; {{ date('Y') }} HIHI-SHOP. Phụ kiện & Thời trang chính hãng cao cấp.</p>
        </footer>

        <!-- Real-time AI Assistant Chatbot -->
        <x-ai-chatbot />

    </body>
</html>
