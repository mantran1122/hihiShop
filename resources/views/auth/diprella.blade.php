@props([
    'initialMode' => 'login'
])

@php
    $isRegister = ($initialMode === 'register') 
        || (old('_form') === 'register') 
        || $errors->has('name') 
        || $errors->has('password_confirmation')
        || request()->routeIs('register');
@endphp

<div class="diprella-scope w-full flex flex-col items-center justify-center font-['Montserrat',sans-serif]">

    <!-- Mobile Switcher Tabs (visible on mobile < 768px) -->
    <div class="md:hidden w-full max-w-sm mb-4 p-1.5 rounded-2xl bg-slate-900/90 border border-slate-800 flex items-center justify-between shadow-lg">
        <button type="button" 
                id="mobile-tab-login"
                onclick="setDiprellaMode('login')"
                class="flex-1 py-2.5 text-xs font-bold rounded-xl leading-none transition-all duration-300 {{ !$isRegister ? 'bg-gradient-to-r from-rose-600 to-rose-500 text-white shadow-md shadow-rose-600/30' : 'text-slate-400 hover:text-slate-200' }}">
            Đăng Nhập
        </button>
        <button type="button" 
                id="mobile-tab-register"
                onclick="setDiprellaMode('register')"
                class="flex-1 py-2.5 text-xs font-bold rounded-xl leading-none transition-all duration-300 {{ $isRegister ? 'bg-gradient-to-r from-rose-600 to-rose-500 text-white shadow-md shadow-rose-600/30' : 'text-slate-400 hover:text-slate-200' }}">
            Đăng Ký
        </button>
    </div>

    <!-- Main Diprella Container (HIHI Shop Dark & Rose Theme) -->
    <div class="diprella-main relative w-full max-w-[1000px] min-h-[660px] md:h-[660px] rounded-[28px] bg-slate-900/95 border border-slate-800/80 shadow-[0_25px_60px_-15px_rgba(0,0,0,0.7)] overflow-hidden p-6 sm:p-8 md:p-0 flex flex-col md:block backdrop-blur-xl" id="diprella-main">
        
        <!-- ==============================================
             CONTAINER A: ĐĂNG KÝ (SIGN UP FORM)
             ============================================== -->
        <div class="diprella-container a-container {{ $isRegister ? 'is-txl is-active' : '' }}" id="a-container">
            <form method="POST" action="{{ route('register') }}" class="diprella-form w-full max-w-[420px] mx-auto flex flex-col items-center justify-center text-center py-4" id="a-form">
                @csrf
                <input type="hidden" name="_form" value="register">

                <h2 class="diprella-title text-2xl sm:text-3xl font-extrabold text-white tracking-tight leading-snug mb-2">
                    Tạo Tài Khoản
                </h2>

                <!-- Social Icons (Google & Facebook ONLY) -->
                <div class="diprella-socials flex items-center justify-center gap-4 my-3">
                    <a href="{{ route('auth.social.redirect', 'google') }}" class="diprella-icon-btn group" title="Đăng ký với Google">
                        <svg class="w-5 h-5 transition-transform group-hover:scale-110" viewBox="0 0 24 24">
                            <path fill="#EA4335" d="M12 5c1.6 0 3 .6 4.1 1.6l3.1-3.1C17.3 1.8 14.8 1 12 1 7.5 1 3.7 3.6 1.9 7.3l3.7 2.9C6.5 7.2 9 5 12 5z"/>
                            <path fill="#4285F4" d="M23.5 12.3c0-.8-.1-1.6-.2-2.3H12v4.5h6.5c-.3 1.5-1.1 2.8-2.4 3.7l3.7 2.9c2.2-2 3.7-5 3.7-8.8z"/>
                            <path fill="#FBBC05" d="M5.6 14.8c-.2-.7-.4-1.5-.4-2.8s.2-2.1.4-2.8L1.9 6.3C.7 8.7 0 10.3 0 12s.7 3.3 1.9 5.7l3.7-2.9z"/>
                            <path fill="#34A853" d="M12 23c3.2 0 6-1.1 8-3l-3.7-2.9c-1.1.7-2.5 1.2-4.3 1.2-3 0-5.5-2.2-6.4-5.2L1.9 16C3.7 19.7 7.5 23 12 23z"/>
                        </svg>
                    </a>
                    <a href="{{ route('auth.social.redirect', 'facebook') }}" class="diprella-icon-btn group" title="Đăng ký với Facebook">
                        <svg class="w-5 h-5 text-[#1877F2] fill-current transition-transform group-hover:scale-110" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                </div>

                <span class="diprella-span text-xs text-slate-400 mb-4 leading-normal">
                    hoặc đăng ký bằng tài khoản email:
                </span>

                <!-- Name Input -->
                <div class="diprella-input-group w-full mb-3">
                    <div class="relative w-full">
                        <input type="text" 
                               name="name" 
                               id="reg_name"
                               value="{{ old('name') }}" 
                               required 
                               autocomplete="name"
                               placeholder="Họ và tên của bạn" 
                               class="diprella-input w-full pl-11 pr-4 py-3 rounded-2xl text-xs sm:text-sm text-slate-100 placeholder-slate-500 bg-slate-950/70 border border-slate-800 focus:outline-none focus:border-rose-500 transition-all">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </span>
                    </div>
                    @if ($errors->has('name'))
                        <p class="text-[11px] text-rose-500 font-semibold text-left mt-1 pl-2">{{ $errors->first('name') }}</p>
                    @endif
                </div>

                <!-- Email Input -->
                <div class="diprella-input-group w-full mb-3">
                    <div class="relative w-full">
                        <input type="email" 
                               name="email" 
                               id="reg_email"
                               value="{{ old('_form') === 'register' ? old('email') : '' }}" 
                               required 
                               autocomplete="username"
                               placeholder="Địa chỉ Email" 
                               class="diprella-input w-full pl-11 pr-4 py-3 rounded-2xl text-xs sm:text-sm text-slate-100 placeholder-slate-500 bg-slate-950/70 border border-slate-800 focus:outline-none focus:border-rose-500 transition-all">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/></svg>
                        </span>
                    </div>
                    @if (old('_form') === 'register' && $errors->has('email'))
                        <p class="text-[11px] text-rose-500 font-semibold text-left mt-1 pl-2">{{ $errors->first('email') }}</p>
                    @endif
                </div>

                <!-- Password Input -->
                <div class="diprella-input-group w-full mb-3">
                    <div class="relative w-full">
                        <input type="password" 
                               name="password" 
                               id="reg_password"
                               required 
                               autocomplete="new-password"
                               placeholder="Mật khẩu (tối thiểu 8 ký tự)" 
                               class="diprella-input w-full pl-11 pr-10 py-3 rounded-2xl text-xs sm:text-sm text-slate-100 placeholder-slate-500 bg-slate-950/70 border border-slate-800 focus:outline-none focus:border-rose-500 transition-all">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </span>
                        <button type="button" onclick="togglePasswordVisibility('reg_password', this)" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-300 transition">
                            <svg class="w-4 h-4 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                    @if (old('_form') === 'register' && $errors->has('password'))
                        <p class="text-[11px] text-rose-500 font-semibold text-left mt-1 pl-2">{{ $errors->first('password') }}</p>
                    @endif
                </div>

                <!-- Password Confirmation Input -->
                <div class="diprella-input-group w-full mb-3">
                    <div class="relative w-full">
                        <input type="password" 
                               name="password_confirmation" 
                               id="reg_password_confirmation"
                               required 
                               autocomplete="new-password"
                               placeholder="Xác nhận lại mật khẩu" 
                               class="diprella-input w-full pl-11 pr-10 py-3 rounded-2xl text-xs sm:text-sm text-slate-100 placeholder-slate-500 bg-slate-950/70 border border-slate-800 focus:outline-none focus:border-rose-500 transition-all">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </span>
                        <button type="button" onclick="togglePasswordVisibility('reg_password_confirmation', this)" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-300 transition">
                            <svg class="w-4 h-4 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                    @if ($errors->has('password_confirmation'))
                        <p class="text-[11px] text-rose-500 font-semibold text-left mt-1 pl-2">{{ $errors->first('password_confirmation') }}</p>
                    @endif
                </div>

                <p class="text-[11px] text-slate-400 mb-4 px-1 leading-5">
                    Bằng việc nhấn Đăng ký, bạn đồng ý với <a href="#" class="text-rose-400 font-semibold hover:underline">Điều khoản</a> & <a href="#" class="text-rose-400 font-semibold hover:underline">Chính sách</a> của HIHI Shop.
                </p>

                <!-- Submit Button with Dynamic Hover Arrow -->
                <button type="submit" class="diprella-btn-submit group w-full py-3.5 rounded-full font-bold text-xs uppercase tracking-[1.5px] text-white transition-all transform active:scale-95 shadow-[0_10px_25px_-5px_rgba(225,29,72,0.4)] flex items-center justify-center gap-2">
                    <span>ĐĂNG KÝ</span>
                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>

                <!-- Mobile Switch Link -->
                <div class="md:hidden mt-4 text-xs text-slate-400 leading-normal">
                    <span>Đã có tài khoản?</span>
                    <button type="button" onclick="setDiprellaMode('login')" class="font-bold text-rose-400 ml-1 underline">
                        Đăng nhập ngay
                    </button>
                </div>
            </form>
        </div>

        <!-- ==============================================
             CONTAINER B: ĐĂNG NHẬP (SIGN IN FORM)
             ============================================== -->
        <div class="diprella-container b-container {{ !$isRegister ? 'is-active' : 'is-txl' }}" id="b-container">
            <form method="POST" action="{{ route('login') }}" class="diprella-form w-full max-w-[420px] mx-auto flex flex-col items-center justify-center text-center py-4" id="b-form">
                @csrf
                <input type="hidden" name="_form" value="login">

                <h2 class="diprella-title text-2xl sm:text-3xl font-extrabold text-white tracking-tight leading-snug mb-2">
                    Đăng Nhập
                </h2>

                <!-- Social Icons (Google & Facebook ONLY) -->
                <div class="diprella-socials flex items-center justify-center gap-4 my-3">
                    <a href="{{ route('auth.social.redirect', 'google') }}" class="diprella-icon-btn group" title="Đăng nhập với Google">
                        <svg class="w-5 h-5 transition-transform group-hover:scale-110" viewBox="0 0 24 24">
                            <path fill="#EA4335" d="M12 5c1.6 0 3 .6 4.1 1.6l3.1-3.1C17.3 1.8 14.8 1 12 1 7.5 1 3.7 3.6 1.9 7.3l3.7 2.9C6.5 7.2 9 5 12 5z"/>
                            <path fill="#4285F4" d="M23.5 12.3c0-.8-.1-1.6-.2-2.3H12v4.5h6.5c-.3 1.5-1.1 2.8-2.4 3.7l3.7 2.9c2.2-2 3.7-5 3.7-8.8z"/>
                            <path fill="#FBBC05" d="M5.6 14.8c-.2-.7-.4-1.5-.4-2.8s.2-2.1.4-2.8L1.9 6.3C.7 8.7 0 10.3 0 12s.7 3.3 1.9 5.7l3.7-2.9z"/>
                            <path fill="#34A853" d="M12 23c3.2 0 6-1.1 8-3l-3.7-2.9c-1.1.7-2.5 1.2-4.3 1.2-3 0-5.5-2.2-6.4-5.2L1.9 16C3.7 19.7 7.5 23 12 23z"/>
                        </svg>
                    </a>
                    <a href="{{ route('auth.social.redirect', 'facebook') }}" class="diprella-icon-btn group" title="Đăng nhập với Facebook">
                        <svg class="w-5 h-5 text-[#1877F2] fill-current transition-transform group-hover:scale-110" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                </div>

                <span class="diprella-span text-xs text-slate-400 mb-4 leading-normal">
                    hoặc đăng nhập bằng tài khoản email:
                </span>

                <!-- Status Session Notification -->
                @if (session('status'))
                    <div class="w-full mb-3 p-3 rounded-2xl bg-emerald-950/80 text-emerald-300 text-xs font-semibold text-center border border-emerald-800">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Email Input -->
                <div class="diprella-input-group w-full mb-3">
                    <div class="relative w-full">
                        <input type="email" 
                               name="email" 
                               id="login_email"
                               value="{{ old('_form') !== 'register' ? old('email') : '' }}" 
                               required 
                               autofocus 
                               autocomplete="username"
                               placeholder="Địa chỉ Email" 
                               class="diprella-input w-full pl-11 pr-4 py-3 rounded-2xl text-xs sm:text-sm text-slate-100 placeholder-slate-500 bg-slate-950/70 border border-slate-800 focus:outline-none focus:border-rose-500 transition-all">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/></svg>
                        </span>
                    </div>
                    @if (old('_form') !== 'register' && $errors->has('email'))
                        <p class="text-[11px] text-rose-500 font-semibold text-left mt-1 pl-2">{{ $errors->first('email') }}</p>
                    @endif
                </div>

                <!-- Password Input -->
                <div class="diprella-input-group w-full mb-3">
                    <div class="relative w-full">
                        <input type="password" 
                               name="password" 
                               id="login_password"
                               required 
                               autocomplete="current-password"
                               placeholder="Mật khẩu" 
                               class="diprella-input w-full pl-11 pr-10 py-3 rounded-2xl text-xs sm:text-sm text-slate-100 placeholder-slate-500 bg-slate-950/70 border border-slate-800 focus:outline-none focus:border-rose-500 transition-all">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </span>
                        <button type="button" onclick="togglePasswordVisibility('login_password', this)" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-300 transition">
                            <svg class="w-4 h-4 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                    @if (old('_form') !== 'register' && $errors->has('password'))
                        <p class="text-[11px] text-rose-500 font-semibold text-left mt-1 pl-2">{{ $errors->first('password') }}</p>
                    @endif
                </div>

                <!-- Remember & Forgot -->
                <div class="w-full flex items-center justify-between text-xs py-1.5 px-1 mb-5 leading-none">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer select-none">
                        <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded text-rose-600 bg-slate-950 border-slate-700 focus:ring-rose-500 cursor-pointer">
                        <span class="ms-2 text-slate-300 font-medium">Ghi nhớ</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="font-semibold text-rose-400 hover:text-rose-300 transition">
                            Quên mật khẩu?
                        </a>
                    @endif
                </div>

                <!-- Submit Button with Dynamic Hover Arrow -->
                <button type="submit" class="diprella-btn-submit group w-full py-3.5 rounded-full font-bold text-xs uppercase tracking-[1.5px] text-white transition-all transform active:scale-95 shadow-[0_10px_25px_-5px_rgba(225,29,72,0.4)] flex items-center justify-center gap-2">
                    <span>ĐĂNG NHẬP</span>
                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>

                <!-- Mobile Switch Link -->
                <div class="md:hidden mt-4 text-xs text-slate-400 leading-normal">
                    <span>Chưa có tài khoản?</span>
                    <button type="button" onclick="setDiprellaMode('register')" class="font-bold text-rose-400 ml-1 underline">
                        Đăng ký ngay
                    </button>
                </div>
            </form>
        </div>

        <!-- ==============================================
             SWITCH OVERLAY PANEL (DESKTOP >= 768px)
             ============================================== -->
        <div class="diprella-switch hidden md:flex {{ $isRegister ? 'is-txr' : '' }}" id="switch-cnt">
            <!-- Decorative concentric glowing rings -->
            <div class="switch__circle {{ $isRegister ? 'is-txr' : '' }}"></div>
            <div class="switch__circle switch__circle--t {{ $isRegister ? 'is-txr' : '' }}"></div>

            <!-- Panel C1 (Shown when on Login view -> Prompts user to Sign Up) -->
            <div class="switch__container {{ $isRegister ? 'is-hidden' : '' }}" id="switch-c1">
                <!-- Animated Floating Icon (Replaced emoji with Dynamic Vector Icon) -->
                <div class="w-16 h-16 rounded-3xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center mx-auto mb-4 shadow-xl">
                    <svg class="w-8 h-8 text-rose-200 animate-float" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>
                <h2 class="switch__title text-3xl font-black text-white mb-3 tracking-tight leading-snug">
                    Chào bạn mới!
                </h2>
                <p class="switch__description text-[12px] text-rose-100/90 leading-5 max-w-[280px] mx-auto mb-8 font-medium">
                    Nhập thông tin cá nhân của bạn và bắt đầu trải nghiệm mua sắm phụ kiện & thời trang đỉnh cao cùng HIHI Shop.
                </p>
                <button type="button" class="switch__btn diprella-btn-toggle leading-none" onclick="changeDiprellaForm()">
                    ĐĂNG KÝ
                </button>
            </div>

            <!-- Panel C2 (Shown when on Register view -> Prompts user to Sign In) -->
            <div class="switch__container {{ !$isRegister ? 'is-hidden' : '' }}" id="switch-c2">
                <!-- Animated Sparkling Icon (Replaced emoji with Dynamic Vector Icon) -->
                <div class="w-16 h-16 rounded-3xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center mx-auto mb-4 shadow-xl">
                    <svg class="w-8 h-8 text-rose-200 animate-pulse-subtle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h2 class="switch__title text-3xl font-black text-white mb-3 tracking-tight leading-snug">
                    Chào mừng trở lại!
                </h2>
                <p class="switch__description text-[12px] text-rose-100/90 leading-5 max-w-[280px] mx-auto mb-8 font-medium">
                    Để tiếp tục theo dõi đơn hàng và tận hưởng ưu đãi riêng biệt, vui lòng đăng nhập vào tài khoản của bạn.
                </p>
                <button type="button" class="switch__btn diprella-btn-toggle leading-none" onclick="changeDiprellaForm()">
                    ĐĂNG NHẬP
                </button>
            </div>
        </div>

    </div>
</div>

<style>
/* ==============================================================
   DIPRELLA AUTH FORM STYLES - HIHI SHOP LUXURY DARK & ROSE THEME
   ============================================================== */

.diprella-scope {
    --form-bg: #0f172a;        /* Deep Slate 900 */
    --input-bg: #030712;       /* Slate 950 */
    --border-color: #1e293b;   /* Slate 800 */
    --brand-rose: #e11d48;     /* Rose 600 */
    --brand-gradient: linear-gradient(135deg, #e11d48 0%, #be123c 50%, #881337 100%);
}

/* Form Container Styles */
@media (min-width: 768px) {
    .diprella-container {
        position: absolute;
        top: 0;
        width: 600px;
        height: 100%;
        padding: 40px 50px;
        background-color: var(--form-bg);
        transition: 1.25s cubic-bezier(0.77, 0, 0.175, 1);
        display: flex;
        justify-content: center;
        align-items: center;
        overflow-y: auto;
    }

    .a-container {
        z-index: 1;
        left: calc(100% - 600px);
        opacity: 0;
        pointer-events: none;
    }

    .b-container {
        left: calc(100% - 600px);
        z-index: 10;
        opacity: 1;
        pointer-events: auto;
    }

    /* Container transformations when active */
    .a-container.is-txl {
        left: 0;
        opacity: 1;
        pointer-events: auto;
        z-index: 10;
    }

    .b-container.is-txl {
        left: 0;
        opacity: 0;
        pointer-events: none;
        z-index: 1;
    }
}

/* Dark Mode Input */
.diprella-input {
    background-color: rgba(3, 7, 18, 0.65);
    border: 1px solid rgba(51, 65, 85, 0.6);
    box-shadow: inset 1px 1px 3px rgba(0, 0, 0, 0.5);
    transition: all 0.3s ease;
}
.diprella-input:focus {
    border-color: rgba(225, 29, 72, 0.45);
    background-color: rgba(3, 7, 18, 0.85);
    box-shadow: inset 1px 1px 3px rgba(0, 0, 0, 0.5), 0 0 15px rgba(225, 29, 72, 0.15);
}

/* Social Circular Button (Google & Facebook) */
.diprella-icon-btn {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background-color: #1e293b;
    border: 1px solid rgba(51, 65, 85, 0.7);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3), inset 1px 1px 1px rgba(255, 255, 255, 0.05);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
}
.diprella-icon-btn:hover {
    background-color: #273549;
    border-color: rgba(225, 29, 72, 0.4);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.4), 0 0 12px rgba(225, 29, 72, 0.2);
    transform: translateY(-2px);
}
.diprella-icon-btn:active {
    transform: translateY(1px);
    box-shadow: inset 1px 1px 3px rgba(0, 0, 0, 0.6);
}

/* Primary Submit Button - Refined Velvety Rose */
.diprella-btn-submit {
    background: linear-gradient(135deg, #be123c 0%, #9f1239 100%);
    box-shadow: 0 8px 20px -4px rgba(190, 18, 60, 0.35);
    transition: all 0.3s ease;
    cursor: pointer;
}
.diprella-btn-submit:hover {
    background: linear-gradient(135deg, #e11d48 0%, #be123c 100%);
    box-shadow: 0 10px 25px -4px rgba(225, 29, 72, 0.45);
    transform: translateY(-1px);
}
.diprella-btn-submit:active {
    transform: translateY(1px);
}

/* Switch Overlay Container with Velvety Deep Burgundy & Rose Tone (dịu mắt, không bị gắt) */
.diprella-switch {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    width: 400px;
    padding: 40px;
    z-index: 50;
    transition: 1.25s cubic-bezier(0.77, 0, 0.175, 1);
    background: linear-gradient(150deg, #180d19 0%, #380818 45%, #6a0b27 100%);
    overflow: hidden;
    box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
}

/* Decorative Rings inside Switch */
.switch__circle {
    position: absolute;
    width: 520px;
    height: 520px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.05);
    box-shadow: inset 8px 8px 16px rgba(0, 0, 0, 0.25), inset -8px -8px 16px rgba(255, 255, 255, 0.15);
    bottom: -60%;
    right: -60%;
    transition: 1.25s cubic-bezier(0.77, 0, 0.175, 1);
    pointer-events: none;
}

.switch__circle--t {
    top: -60%;
    left: -60%;
    bottom: auto;
    right: auto;
}

.switch__circle.is-txr {
    transform: translate(-30%, -20%);
}

.switch__circle--t.is-txr {
    transform: translate(30%, 20%);
}

/* Switch Sub-containers */
.switch__container {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    padding: 40px 30px;
    transition: 1.25s cubic-bezier(0.77, 0, 0.175, 1);
    text-align: center;
}

.switch__container.is-hidden {
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transform: scale(0.9);
}

/* Switch Toggle Button */
.switch__btn {
    width: 180px;
    height: 48px;
    border-radius: 25px;
    font-size: 13px;
    font-weight: 800;
    letter-spacing: 1.5px;
    background-color: #ffffff;
    color: #be123c;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
    transition: all 0.25s ease;
    cursor: pointer;
    border: none;
    outline: none;
}
.switch__btn:hover {
    background-color: #fff1f2;
    transform: scale(1.05);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.35);
}
.switch__btn:active {
    transform: scale(0.96);
}

/* Active Transformation Classes (Diprella Core Animation) */
.diprella-switch.is-txr {
    left: calc(100% - 400px);
}

/* Growth Keyframe Animation during slide */
@keyframes is-gx {
    0%, 10%, 100% {
        width: 400px;
    }
    35%, 65% {
        width: 490px;
    }
}

.diprella-switch.is-gx {
    animation: is-gx 1.25s cubic-bezier(0.77, 0, 0.175, 1);
}

/* Dynamic Subtle Icon Keyframe Animations */
@keyframes floatIcon {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-5px);
    }
}

@keyframes pulseSubtle {
    0%, 100% {
        transform: scale(1);
        opacity: 0.95;
    }
    50% {
        transform: scale(1.08);
        opacity: 1;
    }
}

.animate-float {
    animation: floatIcon 3s ease-in-out infinite;
}

.animate-pulse-subtle {
    animation: pulseSubtle 2.5s ease-in-out infinite;
}

/* Mobile Fallback: toggling active card */
@media (max-width: 767px) {
    .diprella-container {
        display: none;
    }
    .diprella-container.is-active {
        display: block;
    }
}
</style>

<script>
function changeDiprellaForm() {
    const switchCtn = document.getElementById("switch-cnt");
    const switchCircle = document.querySelectorAll(".switch__circle");
    const switchC1 = document.getElementById("switch-c1");
    const switchC2 = document.getElementById("switch-c2");
    const aContainer = document.getElementById("a-container");
    const bContainer = document.getElementById("b-container");

    if (!switchCtn) return;

    // Trigger growth morph effect
    switchCtn.classList.add("is-gx");
    setTimeout(function() {
        switchCtn.classList.remove("is-gx");
    }, 1250);

    // Toggle switch translation
    switchCtn.classList.toggle("is-txr");
    switchCircle.forEach(circle => circle.classList.toggle("is-txr"));

    // Toggle switch text containers
    switchC1.classList.toggle("is-hidden");
    switchC2.classList.toggle("is-hidden");

    // Toggle form positions
    aContainer.classList.toggle("is-txl");
    bContainer.classList.toggle("is-txl");

    // Update active class for mobile
    const isNowRegister = switchCtn.classList.contains("is-txr");
    if (isNowRegister) {
        aContainer.classList.add("is-active");
        bContainer.classList.remove("is-active");
        window.history.pushState({ mode: 'register' }, '', "{{ route('register') }}");
        document.title = "{{ config('app.name', 'HIHI Shop') }} - Tạo Tài Khoản";
    } else {
        bContainer.classList.add("is-active");
        aContainer.classList.remove("is-active");
        window.history.pushState({ mode: 'login' }, '', "{{ route('login') }}");
        document.title = "{{ config('app.name', 'HIHI Shop') }} - Đăng Nhập";
    }

    updateMobileTabs(isNowRegister ? 'register' : 'login');
}

function setDiprellaMode(mode) {
    const switchCtn = document.getElementById("switch-cnt");
    const aContainer = document.getElementById("a-container");
    const bContainer = document.getElementById("b-container");

    if (window.innerWidth >= 768 && switchCtn) {
        const isCurrentRegister = switchCtn.classList.contains("is-txr");
        if ((mode === 'register' && !isCurrentRegister) || (mode === 'login' && isCurrentRegister)) {
            changeDiprellaForm();
        }
    } else {
        // Mobile fallback toggle
        if (mode === 'register') {
            aContainer.classList.add("is-active");
            bContainer.classList.remove("is-active");
            window.history.pushState({ mode: 'register' }, '', "{{ route('register') }}");
            document.title = "{{ config('app.name', 'HIHI Shop') }} - Tạo Tài Khoản";
        } else {
            bContainer.classList.add("is-active");
            aContainer.classList.remove("is-active");
            window.history.pushState({ mode: 'login' }, '', "{{ route('login') }}");
            document.title = "{{ config('app.name', 'HIHI Shop') }} - Đăng Nhập";
        }
        updateMobileTabs(mode);
    }
}

function updateMobileTabs(mode) {
    const tabLogin = document.getElementById("mobile-tab-login");
    const tabRegister = document.getElementById("mobile-tab-register");
    if (!tabLogin || !tabRegister) return;

    if (mode === 'register') {
        tabRegister.className = "flex-1 py-2.5 text-xs font-bold rounded-xl transition-all duration-300 bg-gradient-to-r from-rose-600 to-rose-500 text-white shadow-md shadow-rose-600/30";
        tabLogin.className = "flex-1 py-2.5 text-xs font-bold rounded-xl transition-all duration-300 text-slate-400 hover:text-slate-200";
    } else {
        tabLogin.className = "flex-1 py-2.5 text-xs font-bold rounded-xl transition-all duration-300 bg-gradient-to-r from-rose-600 to-rose-500 text-white shadow-md shadow-rose-600/30";
        tabRegister.className = "flex-1 py-2.5 text-xs font-bold rounded-xl transition-all duration-300 text-slate-400 hover:text-slate-200";
    }
}

function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;
    const isPassword = input.getAttribute("type") === "password";
    input.setAttribute("type", isPassword ? "text" : "password");

    const svg = btn.querySelector("svg");
    if (isPassword) {
        // Show crossed eye
        svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>';
    } else {
        // Normal eye
        svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
    }
}

// Handle Browser Back/Forward navigation
window.addEventListener('popstate', function(event) {
    const isRegisterRoute = window.location.pathname.includes('register');
    setDiprellaMode(isRegisterRoute ? 'register' : 'login');
});
</script>
