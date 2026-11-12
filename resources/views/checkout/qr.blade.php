@extends('layouts.shop')

@section('content')
<div class="py-6 max-w-4xl mx-auto" x-data="vietqrPayment({
    orderCode: '{{ $order->code }}',
    statusUrl: '{{ route('checkout.payment.status', $order->code) }}',
    simulateUrl: '{{ route('checkout.payment.simulate', $order->code) }}',
    initialSeconds: 15 * 60
})">
    
    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-2 text-xs sm:text-sm text-slate-500 mb-6" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-rose-600 transition flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Trang chủ
        </a>
        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('cart.index') }}" class="hover:text-rose-600 transition">Giỏ hàng</a>
        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-900 font-bold">Quét mã QR thanh toán</span>
    </nav>

    <!-- Header bar with countdown -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-rose-950 text-white rounded-3xl p-6 sm:p-8 shadow-xl mb-8 relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-rose-600/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative z-10">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black uppercase tracking-wider bg-rose-500/20 text-rose-300 border border-rose-500/30">
                        VietQR 24/7 NAPAS
                    </span>
                    <span class="text-xs text-slate-400 font-mono">Đơn #{{ $order->code }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                    Quét Mã Thanh Toán Chuyển Khoản
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-lg">
                    Mở ứng dụng ngân hàng bất kỳ (MB, Vietcombank, Techcombank, MoMo...) và quét mã bên dưới để hoàn tất đơn hàng.
                </p>
            </div>

            <!-- Countdown Timer -->
            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/10 text-center shrink-0 min-w-[140px]">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-300 block">Thời gian giữ hàng</span>
                <span class="text-2xl font-black font-mono text-rose-400 block mt-0.5" x-text="formatTimer(countdown)">15:00</span>
                <div class="flex items-center justify-center gap-1.5 mt-1 text-[11px] text-emerald-400 font-medium">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span>Đang chờ chuyển khoản</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main QR Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left Column: The QR Image (5 cols) -->
        <div class="lg:col-span-5 bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-sm text-center">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200/80 mb-4">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                <span>Mã QR Động Đã Nhúng Số Tiền</span>
            </div>

            <!-- QR Frame -->
            <div class="relative mx-auto max-w-[280px] p-3 rounded-2xl bg-white border-2 border-slate-900/10 shadow-lg group">
                <img src="{{ $qrImageUrl }}" alt="VietQR Thanh Toan" class="w-full h-auto rounded-xl object-contain mx-auto transition-transform duration-300 group-hover:scale-[1.02]">
                
                <!-- Corner accent brackets -->
                <div class="absolute top-2 left-2 w-4 h-4 border-t-2 border-l-2 border-rose-600 rounded-tl-sm pointer-events-none"></div>
                <div class="absolute top-2 right-2 w-4 h-4 border-t-2 border-r-2 border-rose-600 rounded-tr-sm pointer-events-none"></div>
                <div class="absolute bottom-2 left-2 w-4 h-4 border-b-2 border-l-2 border-rose-600 rounded-bl-sm pointer-events-none"></div>
                <div class="absolute bottom-2 right-2 w-4 h-4 border-b-2 border-r-2 border-rose-600 rounded-br-sm pointer-events-none"></div>
            </div>

            <!-- Action buttons below QR -->
            <div class="mt-4 flex items-center justify-center gap-3">
                <a href="{{ $qrImageUrl }}" download="VietQR_{{ $order->code }}.png" target="_blank" class="text-xs font-bold text-slate-700 hover:text-rose-600 py-2 px-3 rounded-xl bg-slate-100 hover:bg-rose-50 transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Tải ảnh QR
                </a>
                <button type="button" @click="checkNow()" :disabled="checking" class="text-xs font-bold text-slate-700 hover:text-rose-600 py-2 px-3 rounded-xl bg-slate-100 hover:bg-rose-50 transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" :class="checking ? 'animate-spin text-rose-600' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Kiểm tra lại
                </button>
            </div>

            <div class="mt-5 p-3 rounded-xl bg-slate-50 text-[11px] text-slate-500 leading-relaxed border border-slate-100">
                Khi quét bằng camera ngân hàng, số tiền <strong>{{ number_format($amount, 0, '.', '.') }}đ</strong> và nội dung <strong>{{ $memo }}</strong> sẽ tự động điền mà không cần gõ tay.
            </div>
        </div>

        <!-- Right Column: Manual Transfer Details & Verification (7 cols) -->
        <div class="lg:col-span-7 space-y-6">
            
            <!-- Transfer Information Card -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                    <h2 class="text-base font-bold text-slate-900 uppercase tracking-wider text-xs flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        Thông tin chuyển khoản thủ công
                    </h2>
                    <span class="text-xs text-slate-400">Nếu bạn không thể quét QR</span>
                </div>

                <div class="space-y-4">
                    
                    <!-- Bank Name -->
                    <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Ngân hàng thụ hưởng</span>
                            <strong class="text-sm sm:text-base font-bold text-slate-900 mt-0.5 block">{{ $bankName }} ({{ $bankId }})</strong>
                        </div>
                        <span class="text-xs font-bold text-slate-500 px-2.5 py-1 bg-white rounded-lg border border-slate-200">NAPAS 24/7</span>
                    </div>

                    <!-- Account Number with Copy -->
                    <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 border border-slate-100" x-data="{ copied: false }">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Số tài khoản</span>
                            <strong class="text-base sm:text-lg font-black font-mono text-slate-900 mt-0.5 block tracking-wide">{{ $accountNo }}</strong>
                        </div>
                        <button type="button" @click="copyText('{{ $accountNo }}'); copied = true; setTimeout(() => copied = false, 2000)" class="btn-secondary text-xs px-3 py-1.5 rounded-xl flex items-center gap-1.5 transition">
                            <span x-show="!copied">Sao chép</span>
                            <span x-show="copied" x-cloak class="text-emerald-600 font-bold">✓ Đã chép</span>
                        </button>
                    </div>

                    <!-- Account Name -->
                    <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Tên chủ tài khoản</span>
                            <strong class="text-sm sm:text-base font-bold text-slate-900 mt-0.5 block uppercase">{{ $accountName }}</strong>
                        </div>
                    </div>

                    <!-- Amount with Copy -->
                    <div class="flex items-center justify-between p-3.5 rounded-2xl bg-rose-50/50 border border-rose-200/60" x-data="{ copied: false }">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-rose-500 block">Số tiền chính xác</span>
                            <strong class="text-xl sm:text-2xl font-black text-rose-600 mt-0.5 block">{{ number_format($amount, 0, '.', '.') }} đ</strong>
                        </div>
                        <button type="button" @click="copyText('{{ $amount }}'); copied = true; setTimeout(() => copied = false, 2000)" class="px-3 py-1.5 rounded-xl bg-white border border-rose-200 text-xs font-bold text-rose-700 hover:bg-rose-50 flex items-center gap-1.5 transition shadow-sm">
                            <span x-show="!copied">Sao chép</span>
                            <span x-show="copied" x-cloak class="text-emerald-600 font-bold">✓ Đã chép</span>
                        </button>
                    </div>

                    <!-- Transfer Memo (Crucial) -->
                    <div class="p-4 rounded-2xl bg-amber-50/70 border-2 border-amber-200/80 text-amber-950" x-data="{ copied: false }">
                        <div class="flex items-center justify-between gap-2 mb-1.5">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-amber-800 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-amber-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                Nội dung chuyển khoản (Bắt buộc giữ nguyên)
                            </span>
                            <button type="button" @click="copyText('{{ $memo }}'); copied = true; setTimeout(() => copied = false, 2000)" class="px-2.5 py-1 rounded-lg bg-amber-200/80 hover:bg-amber-300 text-xs font-bold text-amber-900 transition flex items-center gap-1">
                                <span x-show="!copied">Sao chép nội dung</span>
                                <span x-show="copied" x-cloak class="text-emerald-800 font-bold">✓ Đã chép</span>
                            </button>
                        </div>
                        <div class="p-2.5 rounded-xl bg-white/90 border border-amber-300 font-mono text-base font-black text-rose-700 tracking-wider">
                            {{ $memo }}
                        </div>
                        <p class="text-xs text-amber-800 mt-2 leading-relaxed">
                            Vui lòng nhập <strong>chính xác</strong> nội dung trên để hệ thống tự động nhận diện và kích hoạt đơn hàng trong vài giây.
                        </p>
                    </div>

                </div>
            </div>

            <!-- Verification & Action Controls -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-4">
                
                <!-- Polling animation indicator -->
                <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="relative flex items-center justify-center">
                            <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                            <span class="absolute w-5 h-5 rounded-full bg-emerald-500/40 animate-ping"></span>
                        </div>
                        <span class="text-xs font-medium text-slate-600">
                            Hệ thống đang tự động kiểm tra mỗi 3 giây...
                        </span>
                    </div>
                    <span class="text-[11px] font-mono text-slate-400" x-text="pollCount + ' lần quét'"></span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                    <!-- Manual 'I have transferred' button -->
                    <button type="button" @click="simulateConfirm()" :disabled="simulating" class="btn-primary py-3.5 rounded-2xl font-bold text-sm shadow-lg shadow-rose-600/20 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" :class="simulating ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span x-text="simulating ? 'Đang xác thực...' : 'Tôi đã chuyển tiền xong'"></span>
                    </button>

                    <!-- Test simulation button for dev testing -->
                    <button type="button" @click="simulateConfirm(true)" class="btn-secondary py-3.5 rounded-2xl font-bold text-xs text-slate-600 hover:text-slate-900 flex items-center justify-center gap-1.5 border-dashed">
                        <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Mô phỏng thanh toán (Test Dev)</span>
                    </button>
                </div>

                <div class="pt-3 text-center">
                    <a href="{{ route('orders.show', $order->code) }}" class="text-xs font-semibold text-slate-500 hover:text-rose-600 transition inline-flex items-center gap-1">
                        <span>← Xem lại chi tiết đơn hàng hoặc thanh toán sau</span>
                    </a>
                </div>

            </div>

        </div>

    </div>

</div>

@push('scripts')
<script>
function vietqrPayment(config) {
    return {
        countdown: config.initialSeconds,
        checking: false,
        simulating: false,
        pollCount: 0,
        timerInterval: null,
        pollingInterval: null,

        init() {
            // Start countdown
            this.timerInterval = setInterval(() => {
                if (this.countdown > 0) {
                    this.countdown--;
                } else {
                    clearInterval(this.timerInterval);
                    alert('Thời gian giữ đơn hàng đã kết thúc. Vui lòng đặt lại hoặc liên hệ hỗ trợ.');
                }
            }, 1000);

            // Start polling status check every 3.5 seconds
            this.pollingInterval = setInterval(() => {
                this.checkStatus();
            }, 3500);
        },

        formatTimer(seconds) {
            const m = Math.floor(seconds / 60).toString().padStart(2, '0');
            const s = (seconds % 60).toString().padStart(2, '0');
            return `${m}:${s}`;
        },

        copyText(text) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text);
            } else {
                const el = document.createElement('textarea');
                el.value = text;
                document.body.appendChild(el);
                el.select();
                document.execCommand('copy');
                document.body.removeChild(el);
            }
        },

        async checkStatus() {
            this.pollCount++;
            try {
                const res = await fetch(config.statusUrl, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.is_paid) {
                    clearInterval(this.pollingInterval);
                    clearInterval(this.timerInterval);
                    window.location.href = data.redirect_url;
                }
            } catch (e) {
                console.warn('Polling check error:', e);
            }
        },

        async checkNow() {
            this.checking = true;
            await this.checkStatus();
            setTimeout(() => this.checking = false, 600);
        },

        async simulateConfirm(isDev = false) {
            this.simulating = true;
            try {
                const res = await fetch(config.simulateUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    clearInterval(this.pollingInterval);
                    clearInterval(this.timerInterval);
                    window.location.href = data.redirect_url;
                }
            } catch (e) {
                alert('Có lỗi khi xác nhận giao dịch. Vui lòng thử lại.');
                this.simulating = false;
            }
        }
    }
}
</script>
@endpush
@endsection
