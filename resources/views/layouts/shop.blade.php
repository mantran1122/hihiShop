<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'HIHI Shop') }} - Phụ Kiện & Thời Trang Cao Cấp</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/css/home.css', 'resources/js/app.js', 'resources/js/home.js'])

    @stack('head')
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex flex-col antialiased selection:bg-rose-500 selection:text-white @yield('body_class', '')">

    @include('partials.header')

    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 w-full">
        <!-- Toast / Alerts -->
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.300ms class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-emerald-800 flex items-start justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0 text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-emerald-900">Thành công</h4>
                        <p class="text-sm font-medium text-emerald-700 mt-0.5">{{ session('success') }}</p>
                    </div>
                </div>
                <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 p-1 rounded-lg hover:bg-emerald-100/50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.300ms class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200/80 text-rose-800 flex items-start justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center shrink-0 text-rose-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-rose-900">Đã xảy ra lỗi</h4>
                        <p class="text-sm font-medium text-rose-700 mt-0.5">{{ session('error') }}</p>
                    </div>
                </div>
                <button @click="show = false" class="text-rose-500 hover:text-rose-700 p-1 rounded-lg hover:bg-rose-100/50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div x-data="{ show: true }" x-show="show" class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-bold flex items-center gap-2 text-rose-900">
                        <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Vui lòng kiểm tra lại thông tin:
                    </span>
                    <button @click="show = false" class="text-rose-500 hover:text-rose-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <ul class="list-disc list-inside text-sm font-medium space-y-1 text-rose-700 pl-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    @include('partials.footer')

    <!-- Real-time AI Assistant Chatbot -->
    <x-ai-chatbot />

    @stack('scripts')
</body>
</html>
