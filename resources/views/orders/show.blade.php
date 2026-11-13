@extends('layouts.shop')

@section('content')
<div class="py-4 max-w-4xl mx-auto">
    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-2 text-xs sm:text-sm text-slate-500 mb-6" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-rose-600 transition flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Trang chủ
        </a>
        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('orders.index') }}" class="hover:text-rose-600 transition">Đơn mua của tôi</a>
        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-900 font-bold">{{ $order->code }}</span>
    </nav>

    <!-- Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-10 shadow-sm">
        
        <!-- Order Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Mã đơn hàng</span>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mt-0.5">
                    #{{ $order->code }}
                </h1>
            </div>

            @php
                $statusMap = [
                    'pending' => ['Chờ xử lý', 'badge-warning'],
                    'processing' => ['Đang xử lý', 'badge-info'],
                    'shipped' => ['Đang giao hàng', 'badge-purple'],
                    'completed' => ['Đã giao thành công', 'badge-success'],
                    'delivered' => ['Đã giao thành công', 'badge-success'],
                    'cancelled' => ['Đã hủy đơn', 'badge-danger'],
                ];
                $st = $statusMap[$order->status] ?? [$order->status, 'badge-info'];
            @endphp
            <div>
                <span class="{{ $st[1] }} px-3 py-1.5 text-xs font-bold">
                    {{ $st[0] }}
                </span>
            </div>
        </div>

        @if($order->status === 'cancelled')
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                <p class="font-bold">Đơn hàng đã được huỷ</p>
                @if($order->cancel_reason)
                    <p class="mt-1">Lý do: <strong>{{ $order->cancel_reason }}</strong></p>
                @endif
                <p class="mt-1 text-xs text-rose-600">Nếu cần hỗ trợ, vui lòng gọi {{ $siteHotline }} kèm mã đơn.</p>
            </div>
        @endif

        <!-- Order Meta Info -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 my-6 p-5 rounded-2xl bg-slate-50/80 border border-slate-100">
            <div>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Địa chỉ & Vận chuyển</h3>
                @if($order->diaChi)
                    <div class="text-sm text-slate-700 space-y-1">
                        <p><strong>Họ và tên:</strong> {{ $order->diaChi->name }}</p>
                        <p><strong>Điện thoại:</strong> {{ $order->diaChi->phone }}</p>
                        <p><strong>Tỉnh/Thành:</strong> {{ $order->diaChi->province ?: 'N/A' }}</p>
                        <p><strong>Địa chỉ:</strong> {{ $order->diaChi->address_line }}</p>
                        @if($order->shipping_service)
                            <p class="pt-1 text-xs text-rose-600 font-semibold flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Gói: {{ $order->shipping_service }} ({{ $order->shipping_carrier }})</span>
                            </p>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-slate-500">Thông tin theo tài khoản khách hàng.</p>
                @endif
            </div>

            <div>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Thông tin thanh toán</h3>
                @php
                    $provider = $order->payment?->provider ?? 'cod';
                    $isPaid = $order->payment_status === 'paid';
                @endphp
                <div class="text-sm text-slate-700 space-y-1.5">
                    <p><strong>Thời gian đặt:</strong> {{ $order->created_at->format('d/m/Y H:i:s') }}</p>
                    <p>
                        <strong>Hình thức:</strong> 
                        {{ $provider === 'vietqr' ? 'Quét mã VietQR 24/7' : 'Tiền mặt khi nhận hàng (COD)' }}
                    </p>
                    <p class="flex items-center gap-2">
                        <strong>Thanh toán:</strong>
                        <span class="font-bold {{ $isPaid ? 'text-emerald-600' : 'text-amber-600' }}">
                            {{ $isPaid ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                        </span>
                    </p>
                    @if($provider === 'vietqr' && !$isPaid)
                        <div class="pt-2">
                            <a href="{{ route('checkout.payment.qr', $order->code) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 px-3 py-1.5 rounded-xl border border-rose-200 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                <span>Tiếp tục quét mã thanh toán →</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="pt-2">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Sản phẩm trong đơn</h3>
            <div class="divide-y divide-slate-100">
                @foreach($order->mucs as $it)
                    <div class="py-3.5 flex items-center justify-between gap-4 text-sm">
                        <div>
                            <h4 class="font-bold text-slate-900 leading-snug">{{ $it->name_snapshot }}</h4>
                            <span class="text-xs text-slate-500 block mt-0.5">
                                SKU: <code class="px-1.5 py-0.5 rounded bg-slate-100 font-semibold">{{ $it->sku }}</code> • Đơn giá: {{ number_format($it->price, 0, '.', '.') }}đ • SL: <strong>{{ $it->qty }}</strong>
                            </span>
                        </div>
                        <span class="font-bold text-slate-900 whitespace-nowrap">
                            {{ number_format($it->subtotal, 0, '.', '.') }}đ
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Cost Breakdown -->
        <div class="border-t border-slate-100 pt-5 mt-4 space-y-2.5 text-sm text-slate-600">
            <div class="flex items-center justify-between">
                <span>Tiền hàng:</span>
                <span class="font-semibold text-slate-800">{{ number_format($order->subtotal, 0, '.', '.') }}đ</span>
            </div>

            @if($order->discount_total > 0)
                <div class="flex items-center justify-between text-emerald-600 font-medium">
                    <span class="flex items-center gap-1.5">
                        <span>Voucher giảm giá shop:</span>
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
                        <span>Freeship Xtra & Voucher ship:</span>
                        @if($order->shipping_coupon_code)
                            <code class="px-1.5 py-0.2 rounded bg-emerald-100 text-[10px] font-mono font-bold">{{ $order->shipping_coupon_code }}</code>
                        @endif
                    </span>
                    <span class="font-bold">-{{ number_format($order->shipping_discount, 0, '.', '.') }}đ</span>
                </div>
            @endif

            <div class="border-t border-slate-100 pt-3 flex items-baseline justify-between text-base font-bold text-slate-900">
                <span>Tổng tiền đơn hàng:</span>
                <span class="text-2xl font-black text-rose-600">{{ number_format($order->grand_total, 0, '.', '.') }}đ</span>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-between">
            <a href="{{ route('orders.index') }}" class="btn-secondary text-xs sm:text-sm">
                ← Quay lại danh sách đơn hàng
            </a>
            <a href="{{ route('home') }}" class="btn-primary text-xs sm:text-sm">
                Tiếp tục mua sắm →
            </a>
        </div>

    </div>
</div>
@endsection
