@extends('layouts.shop')

@section('content')
<div class="py-6 max-w-3xl mx-auto">
    
    <!-- Success Celebration Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-8 sm:p-12 text-center shadow-sm mb-8">
        <div class="w-20 h-20 rounded-full bg-emerald-50 border-2 border-emerald-200/80 text-emerald-600 flex items-center justify-center text-4xl mx-auto mb-6 shadow-sm">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
        </div>

        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 mb-3">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ $order->payment_status === 'paid' ? 'Đặt hàng & Thanh toán thành công!' : 'Đặt hàng thành công!' }}
        </span>

        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mb-2">
            Cảm ơn bạn đã tin tưởng mua sắm!
        </h1>
        <p class="text-sm sm:text-base text-slate-500 max-w-lg mx-auto mb-8">
            Đơn hàng của bạn đã được ghi nhận trên hệ thống và đang được xử lý để gửi tới bạn trong thời gian sớm nhất.
        </p>

        @php
            $provider = $order->payment?->provider ?? 'cod';
            $isPaid = $order->payment_status === 'paid';
        @endphp

        <!-- Order Meta Summary Chips -->
        <div class="inline-grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-6 bg-slate-50 border border-slate-200/80 rounded-2xl p-4 sm:p-5 text-left w-full max-w-xl mx-auto">
            <div x-data="{ copied: false }">
                <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">Mã đơn hàng</span>
                <div class="flex items-center gap-2 mt-0.5">
                    <strong class="text-base font-black text-rose-600">{{ $order->code }}</strong>
                    <button type="button" @click="navigator.clipboard.writeText('{{ $order->code }}'); copied = true; setTimeout(() => copied = false, 2000)" class="text-slate-400 hover:text-slate-600 text-xs" title="Sao chép">
                        <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                        <span x-show="copied" x-cloak class="text-emerald-600 font-bold">✓</span>
                    </button>
                </div>
            </div>
            <div>
                <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">Thời gian đặt</span>
                <strong class="text-sm font-bold text-slate-800 mt-0.5 block">{{ $order->created_at->format('d/m/Y H:i') }}</strong>
            </div>
            <div>
                <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">Hình thức</span>
                <div class="mt-0.5">
                    @if($provider === 'vietqr')
                        <strong class="text-xs font-bold text-slate-800 block">VietQR 24/7</strong>
                        <span class="text-[11px] font-semibold {{ $isPaid ? 'text-emerald-600' : 'text-amber-600' }}">
                            {{ $isPaid ? '• Đã thanh toán' : '• Chờ chuyển khoản' }}
                        </span>
                    @else
                        <strong class="text-xs font-bold text-slate-800 block">Tiền mặt COD</strong>
                        <span class="text-[11px] font-semibold text-slate-500">• Thu khi nhận</span>
                    @endif
                </div>
            </div>
        </div>

        @if($provider === 'vietqr' && !$isPaid)
            <div class="mt-6">
                <a href="{{ route('checkout.payment.qr', $order->code) }}" class="btn-primary inline-flex items-center gap-2 px-6 py-3 rounded-2xl shadow-lg shadow-rose-600/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    <span>Mở mã VietQR để thanh toán ngay</span>
                </a>
            </div>
        @endif
    </div>

    <!-- Order Details Section -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm mb-8 space-y-6">
        
        <!-- Recipient Info -->
        <div>
            <h2 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-100 uppercase tracking-wider text-xs">
                Thông tin người nhận
            </h2>
            @if($order->diaChi)
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 text-sm">
                    <div>
                        <span class="text-xs text-slate-400 block">Người nhận:</span>
                        <strong class="text-slate-800">{{ $order->diaChi->name }}</strong>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 block">Số điện thoại:</span>
                        <strong class="text-slate-800">{{ $order->diaChi->phone }}</strong>
                    </div>
                    <div class="sm:col-span-1">
                        <span class="text-xs text-slate-400 block">Địa chỉ nhận hàng:</span>
                        <span class="text-slate-800 font-medium">{{ $order->diaChi->address_line }}</span>
                    </div>
                </div>
            @else
                <p class="text-sm text-slate-500 pt-3">Địa chỉ nhận hàng theo thông tin tài khoản khách hàng.</p>
            @endif
        </div>

        <!-- Product Items -->
        <div class="pt-2">
            <h2 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-100 uppercase tracking-wider text-xs">
                Chi tiết các sản phẩm
            </h2>
            <div class="divide-y divide-slate-100 py-2">
                @foreach($order->mucs as $it)
                    <div class="py-3.5 flex items-center justify-between gap-4 text-sm">
                        <div>
                            <h4 class="font-bold text-slate-900 leading-snug">{{ $it->name_snapshot }}</h4>
                            <span class="text-xs text-slate-500 block mt-0.5">
                                SKU: <code class="px-1.5 py-0.5 rounded bg-slate-100 font-semibold">{{ $it->sku }}</code> • Số lượng: <strong>{{ $it->qty }}</strong>
                            </span>
                        </div>
                        <span class="font-bold text-slate-900 whitespace-nowrap">
                            {{ number_format($it->subtotal, 0, '.', '.') }}đ
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Price Totals -->
        <div class="border-t border-slate-100 pt-4 space-y-2.5 text-sm text-slate-600">
            <div class="flex items-center justify-between">
                <span>Tiền hàng:</span>
                <span class="font-semibold text-slate-800">{{ number_format($order->subtotal, 0, '.', '.') }}đ</span>
            </div>

            @if($order->discount_total > 0)
                <div class="flex items-center justify-between text-emerald-600 font-medium">
                    <span class="flex items-center gap-1.5">
                        <span>Voucher giảm giá:</span>
                        @if($order->coupon_code)
                            <code class="px-1.5 py-0.2 rounded bg-emerald-100 text-[10px] font-mono font-bold">{{ $order->coupon_code }}</code>
                        @endif
                    </span>
                    <span class="font-bold">-{{ number_format($order->discount_total, 0, '.', '.') }}đ</span>
                </div>
            @endif

            <div class="flex items-center justify-between">
                <span>Phí vận chuyển ({{ $order->shipping_service ?? 'Tiêu chuẩn' }}):</span>
                <span class="font-semibold text-slate-800">{{ number_format($order->shipping_total, 0, '.', '.') }}đ</span>
            </div>

            @if($order->shipping_discount > 0)
                <div class="flex items-center justify-between text-emerald-600 font-medium">
                    <span class="flex items-center gap-1.5">
                        <span>Ưu đãi Freeship:</span>
                        @if($order->shipping_coupon_code)
                            <code class="px-1.5 py-0.2 rounded bg-emerald-100 text-[10px] font-mono font-bold">{{ $order->shipping_coupon_code }}</code>
                        @endif
                    </span>
                    <span class="font-bold">-{{ number_format($order->shipping_discount, 0, '.', '.') }}đ</span>
                </div>
            @endif

            <div class="border-t border-slate-100 pt-3 flex items-baseline justify-between text-base font-bold text-slate-900">
                <span>Tổng tiền thanh toán:</span>
                <span class="text-2xl font-black text-rose-600">{{ number_format($order->grand_total, 0, '.', '.') }}đ</span>
            </div>
        </div>

    </div>

    <!-- Actions -->
    <div class="flex items-center justify-center gap-4 flex-wrap">
        <a href="{{ route('home') }}" class="btn-primary px-8 py-3 rounded-2xl">
            ← Tiếp tục mua sắm
        </a>
        @auth
            <a href="{{ route('orders.index') }}" class="btn-secondary px-6 py-3 rounded-2xl">
                Quản lý đơn mua của tôi →
            </a>
        @endauth
    </div>

</div>
@endsection
