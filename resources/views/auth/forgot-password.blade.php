<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Quên Mật Khẩu?</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-2 leading-relaxed">
            Đừng lo lắng! Hãy nhập email đã đăng ký tài khoản, chúng tôi sẽ gửi cho bạn liên kết an toàn để đặt lại mật khẩu mới.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Địa chỉ Email của bạn" />
            <div class="relative">
                <x-text-input 
                    id="email" 
                    class="block w-full pl-10" 
                    type="email" 
                    name="email" 
                    :value="old('email')" 
                    placeholder="name@example.com" 
                    required 
                    autofocus 
                />
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/></svg>
                </div>
            </div>
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full py-3 text-sm">
                <span>Gửi liên kết đặt lại mật khẩu</span>
                <span>→</span>
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 pt-5 border-t border-slate-100 text-center">
        <a href="{{ route('login') }}" class="text-xs font-semibold text-slate-500 hover:text-rose-600 transition flex items-center justify-center gap-1">
            <span>← Quay lại trang Đăng nhập</span>
        </a>
    </div>
</x-guest-layout>
