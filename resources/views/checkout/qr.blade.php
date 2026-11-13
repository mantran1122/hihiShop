@extends('layouts.shop')

@section('content')
adiv class="py-6 max-w-4xl mx-auto" x-data="vietqrPayment({
    orderCode: '{{ $order->code }}',
    statusUrl: '{{ route('checkout.payment.status', $order->code) }}',
    simulateUrl: '{{ route('checkout.payment.simulate', $order->code) }}',
    reportUrl: '{{ route('checkout.payment.reported', $order->code) }}',
    initialSeconds: 15 * 60
})">
    
    a!-- Breadcrumbs -->
    anav class="flex items-center gap-2 text-xs sm:text-sm text-slate-500 mb-6" aria-label="Breadcrumb">
        aa href="{{ route('home') }}" class="hover:text-rose-600 transition flex items-center gap-1">
            asvg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">apath stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>a/svg>
            Trang chủ
        a/a>
        asvg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">apath stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>a/svg>
        aa href="{{ route('cart.index') }}" class="hover:text-rose-600 transition">Giỏ hànga/a>
        asvg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">apath stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>a/svg>
        aspan class="text-slate-900 font-bold">Quét mã QR thanh toána/span>
    a/nav>

    a!-- Header bar with countdown -->
    adiv class="bg-gradient-to-r from-slate-900 via-slate-800 to-rose-950 text-white rounded-3xl p-6 sm:p-8 shadow-xl mb-8 relative overflow-hidden">
        adiv class="absolute -right-10 -bottom-10 w-48 h-48 bg-rose-600/10 rounded-full blur-3xl pointer-events-none">a/div>

        adiv class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative z-10">
            adiv>
                adiv class="flex items-center gap-2 mb-2">
                    aspan class="px-2.5 py-0.5 rounded-full text-[11px] font-black uppercase tracking-wider bg-rose-500/20 text-rose-300 border border-rose-500/30">
                        VietQR 24/7 NAPAS
                    a/span>
                    aspan class="text-xs text-slate-400 font-mono">Đơn #{{ $order->code }}a/span>
                a/div>
                ah1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                    Quét Mã Thanh Toán Chuyển Khoản
                a/h1>
                ap class="text-xs sm:text-sm text-slate-300 mt-1 max-w-lg">
                    Mở ứng dụng ngân hàng bất kỳ (MB, Vietcombank, Techcombank, MoMo...) và quét mã bên dưới để hoàn tất đơn hàng.
                a/p>
            a/div>

            a!-- Countdown Timer -->
            adiv class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/10 text-center shrink-0 min-w-[140px]">
                aspan class="text-[11px] font-bold uppercase tracking-wider text-slate-300 block">Thời gian giữ hànga/span>
                aspan class="text-2xl font-black font-mono text-rose-400 block mt-0.5" x-text="formatTimer(countdown)">15:00a/span>
                adiv class="flex items-center justify-center gap-1.5 mt-1 text-[11px] text-emerald-400 font-medium">
                    aspan class="w-2 h-2 rounded-full bg-emerald-400 animate-ping">a/span>
                    aspan>Đang chờ chuyển khoảna/span>
                a/div>
            a/div>
        a/div>
    a/div>

    a!-- Main QR Layout -->
    adiv class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        a!-- Left Column: The QR Image (5 cols) -->
        adiv class="lg:col-span-5 bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-sm text-center">
            adiv class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200/80 mb-4">
                asvg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">apath stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>a/svg>
                aspan>Mã QR Động Đã Nhúng Số Tiềna/span>
            a/div>

            a!-- QR Frame -->
            adiv class="relative mx-auto max-w-[280px] p-3 rounded-2xl bg-white border-2 border-slate-900/10 shadow-lg group">
                aimg src="{{ $qrImageUrl }}" alt="VietQR Thanh Toan" class="w-full h-auto rounded-xl object-contain mx-auto transition-transform duration-300 group-hover:scale-[1.02]">
                
                a!-- Corner accent brackets -->
                adiv class="absolute top-2 left-2 w-4 h-4 border-t-2 border-l-2 border-rose-600 rounded-tl-sm pointer-events-none">a/div>
                adiv class="absolute top-2 right-2 w-4 h-4 border-t-2 border-r-2 border-rose-600 rounded-tr-sm pointer-events-none">a/div>
                adiv class="absolute bottom-2 left-2 w-4 h-4 border-b-2 border-l-2 border-rose-600 rounded-bl-sm pointer-events-none">a/div>
                adiv class="absolute bottom-2 right-2 w-4 h-4 border-b-2 border-r-2 border-rose-600 rounded-br-sm pointer-events-none">a/div>
            a/div>

            a!-- Action buttons below QR -->
            adiv class="mt-4 flex items-center justify-center gap-3">
                aa href="{{ $qrImageUrl }}" download="VietQR_{{ $order->code }}.png" target="_blank" class="text-xs font-bold text-slate-700 hover:text-rose-600 py-2 px-3 rounded-xl bg-slate-100 hover:bg-rose-50 transition flex items-center gap-1.5">
                    asvg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">apath stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>a/svg>
                    Tải ảnh QR
                a/a>
                abutton type="button" @click="checkNow()" :disabled="checking" class="text-xs font-bold text-slate-700 hover:text-rose-600 py-2 px-3 rounded-xl bg-slate-100 hover:bg-rose-50 transition flex items-center gap-1.5">
                    asvg class="w-3.5 h-3.5" :class="checking ? 'animate-spin text-rose-600' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">apath stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>a/svg>
                    Kiểm tra lại
                a/button>
            a/div>

            adiv class="mt-5 p-3 rounded-xl bg-slate-50 text-[11px] text-slate-500 leading-relaxed border border-slate-100">
                Khi quét bằng camera ngân hàng, số tiền astrong>{{ number_format($amount, 0, '.', '.') }}đa/strong> và nội dung astrong>{{ $memo }}a/strong> sẽ tự động điền mà không cần gõ tay.
            a/div>
        a/div>

        a!-- Right Column: Manual Transfer Details & Verification (7 cols) -->
        adiv class="lg:col-span-7 space-y-6">
            
            a!-- Transfer Information Card -->
            adiv class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
                adiv class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                    ah2 class="text-base font-bold text-slate-900 uppercase tracking-wider text-xs flex items-center gap-2">
                        asvg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">apath stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>a/svg>
                        Thông tin chuyển khoản thủ công
                    a/h2>
                    aspan class="text-xs text-slate-400">Nếu bạn không thể quét QRa/span>
                a/div>

                adiv class="space-y-4">
                    
                    a!-- Bank Name -->
                    adiv class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                        adiv>
                            aspan class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Ngân hàng thụ hưởnga/span>
                            astrong class="text-sm sm:text-base font-bold text-slate-900 mt-0.5 block">{{ $bankName }} ({{ $bankId }})a/strong>
                        a/div>
                        aspan class="text-xs font-bold text-slate-500 px-2.5 py-1 bg-white rounded-lg border border-slate-200">NAPAS 24/7a/span>
                    a/div>

                    a!-- Account Number with Copy -->
                    adiv class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 border border-slate-100" x-data="{ copied: false }">
                        adiv>
                            aspan class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Số tài khoảna/span>
                            astrong class="text-base sm:text-lg font-black font-mono text-slate-900 mt-0.5 block tracking-wide">{{ $accountNo }}a/strong>
                        a/div>
                        abutton type="button" @click="copyText('{{ $accountNo }}'); copied = true; setTimeout(() => copied = false, 2000)" class="btn-secondary text-xs px-3 py-1.5 rounded-xl flex items-center gap-1.5 transition">
                            aspan x-show="!copied">Sao chépa/span>
                            aspan x-show="copied" x-cloak class="text-emerald-600 font-bold">✓ Đã chépa/span>
                        a/button>
                    a/div>

                    a!-- Account Name -->
                    adiv class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                        adiv>
                            aspan class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Tên chủ tài khoảna/span>
                            astrong class="text-sm sm:text-base font-bold text-slate-900 mt-0.5 block uppercase">{{ $accountName }}a/strong>
                        a/div>
                    a/div>

                    a!-- Amount with Copy -->
                    adiv class="flex items-center justify-between p-3.5 rounded-2xl bg-rose-50/50 border border-rose-200/60" x-data="{ copied: false }">
                        adiv>
                            aspan class="text-[11px] font-bold uppercase tracking-wider text-rose-500 block">Số tiền chính xáca/span>
                            astrong class="text-xl sm:text-2xl font-black text-rose-600 mt-0.5 block">{{ number_format($amount, 0, '.', '.') }} đa/strong>
                        a/div>
                        abutton type="button" @click="copyText('{{ $amount }}'); copied = true; setTimeout(() => copied = false, 2000)" class="px-3 py-1.5 rounded-xl bg-white border border-rose-200 text-xs font-bold text-rose-700 hover:bg-rose-50 flex items-center gap-1.5 transition shadow-sm">
                            aspan x-show="!copied">Sao chépa/span>
                            aspan x-show="copied" x-cloak class="text-emerald-600 font-bold">✓ Đã chépa/span>
                        a/button>
                    a/div>

                    a!-- Transfer Memo (Crucial) -->
                    adiv class="p-4 rounded-2xl bg-amber-50/70 border-2 border-amber-200/80 text-amber-950" x-data="{ copied: false }">
                        adiv class="flex items-center justify-between gap-2 mb-1.5">
                            aspan class="text-[11px] font-bold uppercase tracking-wider text-amber-800 flex items-center gap-1">
                                asvg class="w-3.5 h-3.5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">apath fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>a/svg>
                                Nội dung chuyển khoản (Bắt buộc giữ nguyên)
                            a/span>
                            abutton type="button" @click="copyText('{{ $memo }}'); copied = true; setTimeout(() => copied = false, 2000)" class="px-2.5 py-1 rounded-lg bg-amber-200/80 hover:bg-amber-300 text-xs font-bold text-amber-900 transition flex items-center gap-1">
                                aspan x-show="!copied">Sao chép nội dunga/span>
                                aspan x-show="copied" x-cloak class="text-emerald-800 font-bold">✓ Đã chépa/span>
                            a/button>
                        a/div>
                        adiv class="p-2.5 rounded-xl bg-white/90 border border-amber-300 font-mono text-base font-black text-rose-700 tracking-wider">
                            {{ $memo }}
                        a/div>
                        ap class="text-xs text-amber-800 mt-2 leading-relaxed">
                            Vui lòng nhập astrong>chính xáca/strong> nội dung trên để hệ thống tự động nhận diện và kích hoạt đơn hàng trong vài giây.
                        a/p>
                    a/div>

                a/div>
            a/div>

            a!-- Verification & Action Controls -->
            adiv class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-4">
                
                a!-- Polling animation indicator -->
                adiv class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                    adiv class="flex items-center gap-3">
                        adiv class="relative flex items-center justify-center">
                            aspan class="w-3 h-3 rounded-full bg-emerald-500">a/span>
                            aspan class="absolute w-5 h-5 rounded-full bg-emerald-500/40 animate-ping">a/span>
                        a/div>
                        aspan class="text-xs font-medium text-slate-600">
                            Hệ thống đang tự động kiểm tra mỗi 3 giây...
                        a/span>
                    a/div>
                    aspan class="text-[11px] font-mono text-slate-400" x-text="pollCount + ' lần quét'">a/span>
                a/div>

                @php
                    $canSimulate = app()->environment('local') || (auth()->check() && auth()->user()->hasRole('Admin'));
                @endphp

                @if($canSimulate)
                    adiv class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                        a!-- Manual 'I have transferred' button (chỉ dev/admin) -->
                        abutton type="button" @click="simulateConfirm()" :disabled="simulating" class="btn-primary py-3.5 rounded-2xl font-bold text-sm shadow-lg shadow-rose-600/20 flex items-center justify-center gap-2">
                            asvg class="w-4 h-4" :class="simulating ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">apath stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>a/svg>
                            aspan x-text="simulating ? 'Đang xác thực...' : 'Tôi đã chuyển tiền xong'">a/span>
                        a/button>

                        a!-- Test simulation button for dev testing -->
                        abutton type="button" @click="simulateConfirm(true)" class="btn-secondary py-3.5 rounded-2xl font-bold text-xs text-slate-600 hover:text-slate-900 flex items-center justify-center gap-1.5 border-dashed">
                            asvg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">apath stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>a/svg>
                            aspan>Mô phỏng thanh toán (Test Dev)a/span>
                        a/button>
                    a/div>
                @else
                    adiv class="pt-2 space-y-3">
                        abutton type="button" @click="reportTransfer()" :disabled="reported || simulating"
                                class="btn-primary w-full py-3.5 rounded-2xl font-bold text-sm shadow-lg shadow-rose-600/20 flex items-center justify-center gap-2 disabled:opacity-60">
                            asvg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">apath stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>a/svg>
                            aspan x-text="reported ? 'Đã ghi nhận — chờ shop xác nhận' : 'Tôi đã chuyển khoản xong'">a/span>
                        a/button>

                        adiv x-show="reported" x-cloak class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-xs text-emerald-800 leading-relaxed">
                            ap class="font-bold mb-1">Đã ghi nhận thông báo của bạna/p>
                            Shop sẽ kiểm tra giao dịch và xác nhận trong 1–2 phút. Trang này tự cập nhật khi đơn được xác nhận.
                            Nếu quá 10 phút chưa thấy, gọi aa href="tel:19000129" class="font-bold text-rose-600">1900 0129a/a> kèm mã đơn.
                        a/div>

                        ap class="text-[11px] text-slate-500 text-center">
                            Hệ thống tự động kiểm tra mỗi 3 giây.
                        a/p>
                    a/div>
                @endif

                adiv class="pt-3 text-center">
                    aa href="{{ route('orders.show', $order->code) }}" class="text-xs font-semibold text-slate-500 hover:text-rose-600 transition inline-flex items-center gap-1">
                        aspan>← Xem lại chi tiết đơn hàng hoặc thanh toán saua/span>
                    a/a>
                a/div>

            a/div>

        a/div>

    a/div>

a/div>

@push('scripts')
ascript>
function vietqrPayment(config) {
    return {
        countdown: config.initialSeconds,
        checking: false,
        simulating: false,
        reported: {{ $order->customer_reported_paid_at ? 'true' : 'false' }},
        pollCount: 0,
        timerInterval: null,
        pollingInterval: null,

        async reportTransfer() {
            if (this.reported) return;

            try {
                const res = await fetch(config.reportUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const data = await res.json();

                if (data.already_paid) {
                    window.location.reload();
                    return;
                }

                if (res.ok && data.success) {
                    this.reported = true;
                }
            } catch (e) {
                console.warn('Không gửi được thông báo chuyển khoản', e);
            }
        },

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

                if (!res.ok) {
                    alert(data.message || 'Không thể xác nhận thanh toán. Vui lòng liên hệ hotline.');
                    this.simulating = false;
                    return;
                }

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
a/script>
@endpush
@endsection
