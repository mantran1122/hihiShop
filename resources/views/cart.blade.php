@extends('layouts.shop')

@section('content')
<div class="py-4">
    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-2 text-xs sm:text-sm text-slate-500 mb-6" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-rose-600 transition flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Trang chủ
        </a>
        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-900 font-bold">Giỏ hàng của bạn</span>
    </nav>

    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mb-6 flex items-center gap-2.5">
        <span>🛒 Giỏ Hàng</span>
        @if($cart && $cart->items->count() > 0)
            <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-rose-50 text-rose-600 border border-rose-200">
                {{ $cart->items->sum('qty') }} sản phẩm
            </span>
        @endif
    </h1>

    @if(!$cart || $cart->items->count() === 0)
        <div class="bg-white rounded-3xl border border-slate-200/80 p-12 text-center max-w-lg mx-auto shadow-sm">
            <div class="w-20 h-20 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">Giỏ hàng của bạn đang trống</h3>
            <p class="text-sm text-slate-500 mb-6">Hãy khám phá các sản phẩm hot và thêm món đồ bạn yêu thích vào giỏ hàng ngay nhé!</p>
            <a href="{{ route('home') }}" class="btn-primary">
                Bắt đầu mua sắm ngay →
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Cart Items List (8 cols) -->
            <div class="lg:col-span-8 flex flex-col gap-4">
                
                <!-- Desktop Table View (hidden on small mobile) -->
                <div class="hidden sm:block bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50/80 text-xs text-slate-500 uppercase tracking-wider border-b border-slate-100">
                            <tr>
                                <th class="p-4 sm:px-6">Sản phẩm</th>
                                <th class="p-4">Đơn giá</th>
                                <th class="p-4 text-center">Số lượng</th>
                                <th class="p-4 text-right">Thành tiền</th>
                                <th class="p-4 text-center">Xóa</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($cart->items as $it)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="p-4 sm:px-6">
                                        <a href="{{ route('product.show', $it->sanPham->slug) }}" class="font-bold text-slate-900 hover:text-rose-600 transition block">
                                            {{ $it->sanPham->name }}
                                        </a>
                                        <span class="text-xs text-slate-500 font-medium inline-block mt-0.5">
                                            SKU: <code class="px-1.5 py-0.5 rounded bg-slate-100 font-semibold text-slate-600">{{ $it->bienThe->sku }}</code>
                                        </span>
                                    </td>
                                    <td class="p-4 font-semibold text-slate-700 whitespace-nowrap">
                                        {{ number_format($it->unit_price, 0, '.', '.') }}đ
                                    </td>
                                    <td class="p-4">
                                        <form method="post" action="{{ route('cart.update') }}" class="flex items-center justify-center gap-1.5 m-0">
                                            @csrf
                                            <input type="hidden" name="item_id" value="{{ $it->id }}">
                                            <input type="number" name="qty" value="{{ $it->qty }}" min="1" max="100" class="w-14 px-2 py-1.5 text-center text-xs font-bold border border-slate-200 rounded-lg focus:ring-rose-500 focus:border-rose-500">
                                            <button type="submit" class="px-2.5 py-1.5 text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition" title="Cập nhật số lượng">
                                                Lưu
                                            </button>
                                        </form>
                                    </td>
                                    <td class="p-4 text-right font-black text-rose-600 whitespace-nowrap">
                                        {{ number_format($it->line_total, 0, '.', '.') }}đ
                                    </td>
                                    <td class="p-4 text-center">
                                        <form method="post" action="{{ route('cart.remove') }}" onsubmit="return confirm('Bạn chắc chắn muốn xóa sản phẩm này?');" class="m-0">
                                            @csrf
                                            <input type="hidden" name="item_id" value="{{ $it->id }}">
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Xóa">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View (shown only on small screens) -->
                <div class="sm:hidden space-y-3">
                    @foreach($cart->items as $it)
                        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm flex flex-col gap-3">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <a href="{{ route('product.show', $it->sanPham->slug) }}" class="font-bold text-slate-900 text-sm hover:text-rose-600 transition">
                                        {{ $it->sanPham->name }}
                                    </a>
                                    <div class="text-xs text-slate-500 mt-0.5">
                                        SKU: <code class="px-1 py-0.5 rounded bg-slate-100 font-semibold">{{ $it->bienThe->sku }}</code>
                                    </div>
                                </div>
                                <form method="post" action="{{ route('cart.remove') }}" onsubmit="return confirm('Bạn chắc chắn muốn xóa sản phẩm này?');" class="m-0">
                                    @csrf
                                    <input type="hidden" name="item_id" value="{{ $it->id }}">
                                    <button type="submit" class="text-red-500 hover:text-red-700 p-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>

                            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                                <form method="post" action="{{ route('cart.update') }}" class="flex items-center gap-1.5 m-0">
                                    @csrf
                                    <input type="hidden" name="item_id" value="{{ $it->id }}">
                                    <span class="text-xs text-slate-500">SL:</span>
                                    <input type="number" name="qty" value="{{ $it->qty }}" min="1" max="100" class="w-12 px-1.5 py-1 text-center text-xs font-bold border border-slate-200 rounded-lg">
                                    <button type="submit" class="px-2 py-1 text-xs font-bold bg-slate-100 text-slate-700 rounded-lg">Lưu</button>
                                </form>

                                <div class="text-right">
                                    <span class="text-xs text-slate-400 block">{{ number_format($it->unit_price, 0, '.', '.') }}đ / sp</span>
                                    <span class="text-sm font-black text-rose-600">{{ number_format($it->line_total, 0, '.', '.') }}đ</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pt-2">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-slate-600 hover:text-rose-600 transition">
                        <span>← Tiếp tục chọn thêm sản phẩm</span>
                    </a>
                </div>
            </div>

            <!-- Order Summary (4 cols) -->
            <div class="lg:col-span-4 sticky top-24">
                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
                    <h2 class="text-base font-bold text-slate-900 pb-4 border-b border-slate-100 uppercase tracking-wider text-xs">
                        Tóm tắt đơn hàng
                    </h2>

                    <div class="py-4 space-y-3 text-sm text-slate-600">
                        <div class="flex items-center justify-between">
                            <span>Tạm tính:</span>
                            <span class="font-bold text-slate-900">{{ number_format($cart->subtotal, 0, '.', '.') }}đ</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Phí vận chuyển:</span>
                            <span class="badge-success text-xs font-bold px-2 py-0.5">Miễn phí (COD)</span>
                        </div>
                        <div class="border-t border-slate-100 pt-3 flex items-baseline justify-between">
                            <span class="text-sm font-bold text-slate-900">Tổng thanh toán:</span>
                            <span class="text-2xl font-black text-rose-600">{{ number_format($cart->subtotal, 0, '.', '.') }}đ</span>
                        </div>
                    </div>

                    <a href="{{ route('checkout.index') }}" class="btn-primary w-full py-3 text-sm rounded-xl font-bold flex items-center justify-center gap-2 mt-2">
                        <span>Tiến hành thanh toán</span>
                        <span>→</span>
                    </a>

                    <div class="mt-4 pt-4 border-t border-slate-100 text-xs text-slate-500 space-y-2">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Thanh toán COD khi nhận hàng</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Kiểm tra hàng trước khi thanh toán</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    @endif
</div>
@endsection
