@extends('layouts.shop')

@section('content')
<div class="py-4 max-w-5xl mx-auto" x-data="checkoutManager({
    calculateUrl: '{{ route('checkout.calculate') }}',
    csrfToken: '{{ csrf_token() }}',
    initialSubtotal: {{ $subtotal }},
    initialItemCount: {{ $itemCount }},
    initialProvince: '{{ $defaultProvince }}',
    initialRates: @js($shippingData['methods']),
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
        <span class="text-slate-900 font-bold">Thanh toán</span>
    </nav>

    <!-- Header title -->
    <div class="flex items-center justify-between gap-4 mb-8 flex-wrap">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-rose-50 border border-rose-200/60 flex items-center justify-center text-rose-600 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                    Đặt Hàng & Thanh Toán
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Cơ chế vận chuyển Shopee Xtra & Kho Voucher ưu đãi tại Việt Nam</p>
            </div>
        </div>

        <!-- Freeship badge indicator -->
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200/80 shadow-sm">
            <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
            <span>Shopee Freeship Xtra: Đã kích hoạt</span>
        </div>
    </div>

    <form method="post" action="{{ route('checkout.place') }}">
        @csrf
        <input type="hidden" name="shipping_service" :value="shippingService">
        <input type="hidden" name="payment_method" :value="paymentMethod">
        <input type="hidden" name="coupon_code" :value="couponCode">
        <input type="hidden" name="shipping_coupon_code" :value="shippingCouponCode">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Col 1: Shipping Address, Carrier & Voucher Selection (7 cols) -->
            <div class="lg:col-span-7 space-y-6">

                <!-- Step 1: Recipient & Location Info -->
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
                    <div class="flex items-center justify-between gap-3 mb-6 pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-full bg-rose-100 text-rose-600 font-black text-sm flex items-center justify-center">1</span>
                            <h2 class="text-lg font-bold text-slate-900">Địa chỉ nhận hàng</h2>
                        </div>
                        <span class="text-xs font-medium text-slate-400">Kho gửi: Hà Nội</span>
                    </div>

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="name" class="form-label">Họ và tên người nhận <span class="text-rose-600">*</span></label>
                                <div class="relative">
                                    <input type="text" id="name" name="name" value="{{ old('name', Auth::user()?->name) }}" placeholder="Ví dụ: Nguyễn Văn A" required class="form-input pl-10">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="phone" class="form-label">Số điện thoại liên hệ <span class="text-rose-600">*</span></label>
                                <div class="relative">
                                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Ví dụ: 0912345678" required class="form-input pl-10">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Province selection dropdown (Shopee zone calculation) -->
                        <div>
                            <label for="province" class="form-label flex items-center justify-between">
                                <span>Tỉnh / Thành phố nhận hàng <span class="text-rose-600">*</span></span>
                                <span class="text-[11px] text-slate-400 font-normal">Hệ thống tự động tính cước vận chuyển chuẩn xác</span>
                            </label>
                            <div class="relative">
                                <select id="province" name="province" x-model="province" @change="recalculate()" required class="form-select pl-10 text-sm font-semibold text-slate-800">
                                    @foreach($provincesList as $prov)
                                        <option value="{{ $prov }}">{{ $prov }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="address" class="form-label">Địa chỉ nhận hàng chi tiết <span class="text-rose-600">*</span></label>
                            <textarea id="address" name="address" rows="2" placeholder="Số nhà, tên ngõ/tòa nhà, tên đường, phường/xã, quận/huyện" required class="form-input text-sm">{{ old('address') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Shopee Shipping Carrier & Methods -->
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
                    <div class="flex items-center justify-between gap-3 mb-6 pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-full bg-rose-100 text-rose-600 font-black text-sm flex items-center justify-center">2</span>
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">Phương thức vận chuyển</h2>
                                <p class="text-xs text-slate-500 mt-0.5">Liên kết hệ sinh thái giao hàng Shopee Express & GHN</p>
                            </div>
                        </div>
                        <div x-show="calculating" class="flex items-center gap-1.5 text-xs text-rose-600 font-semibold" x-cloak>
                            <svg class="w-3.5 h-3.5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span>Đang tính cước...</span>
                        </div>
                    </div>

                    <!-- Shipping Rate Cards -->
                    <div class="space-y-3">
                        <template x-for="(service, key) in rates" :key="key">
                            <label class="relative flex items-start justify-between gap-4 p-4 rounded-2xl border-2 cursor-pointer transition-all duration-200"
                                :class="shippingService === key ? 'border-rose-500 bg-rose-50/20 shadow-sm' : 'border-slate-200 hover:border-slate-300 bg-white'">
                                <div class="flex items-start gap-3">
                                    <input type="radio" name="_shipping_service_radio" :value="key" x-model="shippingService" @change="recalculate()" class="mt-1 text-rose-600 focus:ring-rose-500 w-4 h-4 border-slate-300">
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <strong class="text-sm font-bold text-slate-900" x-text="service.name"></strong>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold"
                                                :class="key === 'fast' ? 'bg-rose-100 text-rose-700' : (key === 'instant' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700')"
                                                x-text="service.badge"></span>
                                        </div>
                                        <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span x-text="service.time_text"></span>
                                            <span class="text-slate-300">•</span>
                                            <span class="font-medium text-slate-600" x-text="service.carrier"></span>
                                        </p>
                                    </div>
                                </div>

                                <!-- Rate price calculation with Freeship strikethrough -->
                                <div class="text-right shrink-0">
                                    <div class="flex items-baseline justify-end gap-1.5">
                                        <span x-show="service.discount > 0" class="text-xs text-slate-400 line-through font-mono" x-text="formatMoney(service.base_fee) + 'đ'"></span>
                                        <strong class="text-sm sm:text-base font-black text-rose-600 font-mono" x-text="service.final_fee === 0 ? 'Miễn phí' : formatMoney(service.final_fee) + 'đ'"></strong>
                                    </div>
                                    <span x-show="service.discount > 0" class="text-[10px] font-bold text-emerald-600 block mt-0.5" x-text="'Tiết kiệm ' + formatMoney(service.discount) + 'đ'"></span>
                                </div>
                            </label>
                        </template>
                    </div>

                    <!-- Freeship Xtra Notification Banner -->
                    <div class="mt-4 p-3.5 rounded-2xl bg-amber-50/80 border border-amber-200/80 text-xs text-amber-900 flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
                        <div>
                            <strong class="font-bold text-amber-950 block">Chính sách Shopee Freeship Xtra tại HIHI Shop</strong>
                            <span class="text-amber-800 leading-relaxed block mt-0.5">
                                Đơn từ <strong>250.000đ</strong> giảm 15.000đ phí ship • Đơn từ <strong>450.000đ</strong> giảm 30.000đ phí ship (Miễn phí toàn bộ gói Tiêu chuẩn & Tiết kiệm).
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Shopee Voucher Center -->
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
                    <div class="flex items-center justify-between gap-3 mb-6 pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-full bg-rose-100 text-rose-600 font-black text-sm flex items-center justify-center">3</span>
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">Shopee Voucher & Khuyến Mãi</h2>
                                <p class="text-xs text-slate-500 mt-0.5">Áp dụng song song Voucher Tiền hàng & Voucher Vận chuyển</p>
                            </div>
                        </div>
                        <button type="button" @click="showVoucherModal = true" class="text-xs font-bold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 px-3 py-1.5 rounded-xl border border-rose-200 transition flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                            <span>Kho Voucher ({{ count($shopVouchers) + count($shippingVouchers) }})</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        
                        <!-- Voucher Slot 1: Shop Discount Voucher -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Voucher Cửa Hàng (Tiền hàng)</span>
                            <div class="flex items-center gap-2">
                                <input type="text" x-model="inputCoupon" placeholder="Nhập mã giảm giá..." class="form-input text-xs font-mono uppercase font-bold py-2">
                                <button type="button" @click="applyShopVoucher(inputCoupon)" class="btn-primary py-2 px-3 text-xs whitespace-nowrap rounded-xl">
                                    Áp dụng
                                </button>
                            </div>

                            <!-- Applied Tag -->
                            <div x-show="couponCode" class="mt-2.5 p-2 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-between text-xs" x-cloak>
                                <div class="flex items-center gap-1.5 text-emerald-800">
                                    <span class="font-bold font-mono text-emerald-700" x-text="couponCode"></span>
                                    <span class="text-slate-400">•</span>
                                    <span class="font-semibold" x-text="'-' + formatMoney(breakdown.order_discount) + 'đ'"></span>
                                </div>
                                <button type="button" @click="removeShopVoucher()" class="text-slate-400 hover:text-rose-600 font-bold px-1" title="Hủy mã">✕</button>
                            </div>
                            <span x-show="orderCouponMsg" class="text-[11px] block mt-1.5" :class="orderCouponValid ? 'text-emerald-600' : 'text-rose-600'" x-text="orderCouponMsg"></span>
                        </div>

                        <!-- Voucher Slot 2: Freeship Voucher -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Voucher Miễn Phí Vận Chuyển</span>
                            <div class="flex items-center gap-2">
                                <input type="text" x-model="inputShippingCoupon" placeholder="Nhập mã Freeship..." class="form-input text-xs font-mono uppercase font-bold py-2">
                                <button type="button" @click="applyShipVoucher(inputShippingCoupon)" class="btn-primary py-2 px-3 text-xs whitespace-nowrap rounded-xl">
                                    Áp dụng
                                </button>
                            </div>

                            <!-- Applied Tag -->
                            <div x-show="shippingCouponCode" class="mt-2.5 p-2 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-between text-xs" x-cloak>
                                <div class="flex items-center gap-1.5 text-emerald-800">
                                    <span class="font-bold font-mono text-emerald-700" x-text="shippingCouponCode"></span>
                                    <span class="text-slate-400">•</span>
                                    <span class="font-semibold" x-text="'-' + formatMoney(breakdown.voucher_shipping_discount) + 'đ'"></span>
                                </div>
                                <button type="button" @click="removeShipVoucher()" class="text-slate-400 hover:text-rose-600 font-bold px-1" title="Hủy mã">✕</button>
                            </div>
                            <span x-show="shippingCouponMsg" class="text-[11px] block mt-1.5" :class="shippingCouponValid ? 'text-emerald-600' : 'text-rose-600'" x-text="shippingCouponMsg"></span>
                        </div>

                    </div>
                </div>

                <!-- Step 4: Payment Method Selection -->
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-full bg-rose-100 text-rose-600 font-black text-sm flex items-center justify-center">4</span>
                            <h2 class="text-lg font-bold text-slate-900">Phương thức thanh toán</h2>
                        </div>
                        <span class="text-xs text-slate-400 font-medium">Bảo mật SSL 256-bit</span>
                    </div>

                    <div class="space-y-3.5">
                        <!-- Option 1: VietQR Payment (Recommended) -->
                        <label class="relative flex items-start gap-4 p-4.5 sm:p-5 rounded-2xl border-2 cursor-pointer transition-all duration-200"
                            :class="paymentMethod === 'vietqr' ? 'border-rose-500 bg-rose-50/30 shadow-md shadow-rose-500/5' : 'border-slate-200 hover:border-slate-300 bg-white'">
                            <input type="radio" name="_payment_method_radio" value="vietqr" x-model="paymentMethod" class="mt-1 text-rose-600 focus:ring-rose-500 w-4 h-4 border-slate-300">
                            
                            <div class="flex-1">
                                <div class="flex items-center justify-between gap-2 flex-wrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-rose-500 to-rose-700 text-white flex items-center justify-center shrink-0 shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                        </div>
                                        <strong class="text-sm sm:text-base font-bold text-slate-900">Quét mã QR Ngân Hàng (VietQR 24/7)</strong>
                                    </div>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-700 border border-rose-200">
                                        <svg class="w-3 h-3 text-rose-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
                                        Khuyên dùng
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                    Hỗ trợ quét trên <strong>40+ ứng dụng ngân hàng</strong> (MB, VCB, Techcombank, VPBank, MoMo...). Điền sẵn tiền và nội dung tự động.
                                </p>
                            </div>
                        </label>

                        <!-- Option 2: COD Payment -->
                        <label class="relative flex items-start gap-4 p-4.5 sm:p-5 rounded-2xl border-2 cursor-pointer transition-all duration-200"
                            :class="paymentMethod === 'cod' ? 'border-rose-500 bg-rose-50/30 shadow-md shadow-rose-500/5' : 'border-slate-200 hover:border-slate-300 bg-white'">
                            <input type="radio" name="_payment_method_radio" value="cod" x-model="paymentMethod" class="mt-1 text-rose-600 focus:ring-rose-500 w-4 h-4 border-slate-300">
                            
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    </div>
                                    <strong class="text-sm sm:text-base font-bold text-slate-900">Thanh toán khi nhận hàng (COD)</strong>
                                </div>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                    Thanh toán tiền mặt cho nhân viên giao hàng khi bưu tá mang kiện hàng đến tận nơi.
                                </p>
                            </div>
                        </label>
                    </div>

                    <div class="mt-6">
                        <button class="btn-primary w-full py-4 text-base rounded-2xl font-bold shadow-lg shadow-rose-600/25 flex items-center justify-center gap-2" type="submit">
                            <span x-show="paymentMethod === 'vietqr'">Tiến hành Quét mã QR Thanh toán →</span>
                            <span x-show="paymentMethod === 'cod'" x-cloak>Xác nhận đặt hàng COD →</span>
                        </button>
                    </div>
                </div>

            </div>

            <!-- Col 2: Order Breakdown & Calculation Table (5 cols) -->
            <div class="lg:col-span-5 bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm sticky top-24">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                    <span class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 font-black text-sm flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </span>
                    <h2 class="text-lg font-bold text-slate-900">Chi tiết kiện hàng ({{ $itemCount }} món)</h2>
                </div>

                <!-- Items list -->
                <div class="divide-y divide-slate-100 max-h-60 overflow-y-auto no-scrollbar pr-1 mb-5">
                    @foreach($cart->mucs as $it)
                        <div class="py-2.5 flex items-start justify-between gap-3 text-sm">
                            <div>
                                <h4 class="font-bold text-slate-900 leading-snug text-xs sm:text-sm">{{ $it->sanPham->name }}</h4>
                                <span class="text-[11px] text-slate-500 block mt-0.5">
                                    SKU: <code class="px-1 py-0.2 rounded bg-slate-100 font-semibold">{{ $it->bienThe->sku }}</code> • SL: <strong>{{ $it->qty }}</strong>
                                </span>
                            </div>
                            <span class="font-bold text-slate-900 whitespace-nowrap text-xs sm:text-sm">
                                {{ number_format($it->line_total, 0, '.', '.') }}đ
                            </span>
                        </div>
                    @endforeach
                </div>

                <!-- Shopee Calculation Matrix Table -->
                <div class="border-t border-slate-100 pt-4 space-y-3 text-sm text-slate-600">
                    <div class="flex items-center justify-between">
                        <span>Tiền hàng:</span>
                        <span class="font-semibold text-slate-900" x-text="formatMoney(breakdown.subtotal) + 'đ'"></span>
                    </div>

                    <!-- Order Discount -->
                    <div x-show="breakdown.order_discount > 0" class="flex items-center justify-between text-emerald-600" x-cloak>
                        <span class="flex items-center gap-1">
                            <span>Voucher giảm giá shop:</span>
                            <span class="px-1.5 py-0.2 rounded bg-emerald-100 text-[10px] font-mono font-bold" x-text="couponCode"></span>
                        </span>
                        <span class="font-bold font-mono" x-text="'-' + formatMoney(breakdown.order_discount) + 'đ'"></span>
                    </div>

                    <!-- Base Shipping fee -->
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-1">
                            <span>Phí vận chuyển:</span>
                            <span class="text-[11px] text-slate-400 font-medium" x-text="'(' + (rates[shippingService]?.name || '') + ')'"></span>
                        </span>
                        <span class="font-semibold text-slate-900 font-mono" x-text="formatMoney(breakdown.base_shipping_fee) + 'đ'"></span>
                    </div>

                    <!-- Shipping Discount (Freeship Xtra + Voucher) -->
                    <div x-show="breakdown.total_shipping_discount > 0" class="flex items-center justify-between text-emerald-600" x-cloak>
                        <span class="flex items-center gap-1">
                            <span>Freeship Xtra & Voucher ship:</span>
                        </span>
                        <span class="font-bold font-mono" x-text="'-' + formatMoney(breakdown.total_shipping_discount) + 'đ'"></span>
                    </div>

                    <!-- Final Shipping Net -->
                    <div class="flex items-center justify-between text-xs text-slate-500 bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                        <span>Cước ship thanh toán:</span>
                        <strong class="font-bold text-slate-800" x-text="breakdown.final_shipping_fee === 0 ? 'Miễn phí 0đ' : formatMoney(breakdown.final_shipping_fee) + 'đ'"></strong>
                    </div>

                    <!-- Grand Total -->
                    <div class="border-t border-slate-100 pt-3 flex items-baseline justify-between">
                        <span class="text-base font-bold text-slate-900">Tổng thanh toán:</span>
                        <span class="text-2xl font-black text-rose-600 font-mono" x-text="formatMoney(breakdown.grand_total) + 'đ'"></span>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100">
                    <a href="{{ route('cart.index') }}" class="text-xs font-semibold text-slate-500 hover:text-rose-600 transition flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>Quay lại chỉnh sửa giỏ hàng</span>
                    </a>
                </div>
            </div>

        </div>
    </form>

    <!-- Modal Kho Voucher Shopee Style -->
    <div x-show="showVoucherModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Backdrop -->
            <div @click="showVoucherModal = false" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Panel -->
            <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-200">
                <div class="p-6 sm:p-8">
                    
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                        <div>
                            <h3 class="text-xl font-black text-slate-900">Kho Voucher HIHI Shopee</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Chọn voucher ưu đãi phù hợp nhất cho đơn hàng của bạn</p>
                        </div>
                        <button type="button" @click="showVoucherModal = false" class="text-slate-400 hover:text-slate-600 font-bold p-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Voucher Tabs -->
                    <div class="flex items-center gap-2 p-1 bg-slate-100 rounded-2xl mb-6">
                        <button type="button" @click="voucherTab = 'shipping'" class="flex-1 py-2 text-xs font-bold rounded-xl transition"
                            :class="voucherTab === 'shipping' ? 'bg-white text-rose-600 shadow-sm' : 'text-slate-600 hover:text-slate-900'">
                            Mã Vận Chuyển (Freeship)
                        </button>
                        <button type="button" @click="voucherTab = 'shop'" class="flex-1 py-2 text-xs font-bold rounded-xl transition"
                            :class="voucherTab === 'shop' ? 'bg-white text-rose-600 shadow-sm' : 'text-slate-600 hover:text-slate-900'">
                            Mã Giảm Giá Shop
                        </button>
                    </div>

                    <!-- Tab 1: Freeship Vouchers -->
                    <div x-show="voucherTab === 'shipping'" class="space-y-3 max-h-96 overflow-y-auto no-scrollbar pr-1">
                        @foreach($shippingVouchers as $sv)
                            <div class="p-4 rounded-2xl border border-slate-200/80 hover:border-rose-400 bg-white transition flex items-center justify-between gap-3 shadow-xs">
                                <div class="flex items-start gap-3">
                                    <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-200 flex flex-col items-center justify-center text-amber-700 shrink-0">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
                                        <span class="text-[9px] font-black uppercase">FREESHIP</span>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <strong class="text-sm font-bold text-slate-900">{{ $sv->title }}</strong>
                                            <code class="px-1.5 py-0.2 rounded bg-slate-100 text-[11px] font-mono font-bold text-rose-600">{{ $sv->code }}</code>
                                        </div>
                                        <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">{{ $sv->description }}</p>
                                        <span class="text-[10px] text-slate-400 block mt-1">Đơn tối thiểu: {{ number_format($sv->min_order, 0, '.', '.') }}đ</span>
                                    </div>
                                </div>
                                <button type="button" @click="applyShipVoucher('{{ $sv->code }}'); showVoucherModal = false" class="btn-secondary text-xs px-3.5 py-2 rounded-xl whitespace-nowrap hover:bg-rose-50 hover:text-rose-600 hover:border-rose-300 font-bold">
                                    Áp dụng
                                </button>
                            </div>
                        @endforeach
                    </div>

                    <!-- Tab 2: Shop Vouchers -->
                    <div x-show="voucherTab === 'shop'" class="space-y-3 max-h-96 overflow-y-auto no-scrollbar pr-1" x-cloak>
                        @foreach($shopVouchers as $ov)
                            <div class="p-4 rounded-2xl border border-slate-200/80 hover:border-rose-400 bg-white transition flex items-center justify-between gap-3 shadow-xs">
                                <div class="flex items-start gap-3">
                                    <div class="w-12 h-12 rounded-2xl bg-rose-50 border border-rose-200 flex flex-col items-center justify-center text-rose-600 shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                                        <span class="text-[9px] font-black uppercase">VOUCHER</span>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <strong class="text-sm font-bold text-slate-900">{{ $ov->title }}</strong>
                                            <code class="px-1.5 py-0.2 rounded bg-slate-100 text-[11px] font-mono font-bold text-rose-600">{{ $ov->code }}</code>
                                        </div>
                                        <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">{{ $ov->description }}</p>
                                        <span class="text-[10px] text-slate-400 block mt-1">Đơn tối thiểu: {{ number_format($ov->min_order, 0, '.', '.') }}đ</span>
                                    </div>
                                </div>
                                <button type="button" @click="applyShopVoucher('{{ $ov->code }}'); showVoucherModal = false" class="btn-secondary text-xs px-3.5 py-2 rounded-xl whitespace-nowrap hover:bg-rose-50 hover:text-rose-600 hover:border-rose-300 font-bold">
                                    Áp dụng
                                </button>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-100 text-center">
                        <button type="button" @click="showVoucherModal = false" class="btn-primary w-full py-3 rounded-2xl text-xs font-bold">
                            Đóng kho voucher
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function checkoutManager(config) {
    return {
        province: config.initialProvince,
        shippingService: 'fast',
        paymentMethod: 'vietqr',
        couponCode: '',
        shippingCouponCode: '',
        inputCoupon: '',
        inputShippingCoupon: '',
        orderCouponValid: false,
        orderCouponMsg: '',
        shippingCouponValid: false,
        shippingCouponMsg: '',
        showVoucherModal: false,
        voucherTab: 'shipping',
        calculating: false,
        rates: config.initialRates,
        breakdown: {
            subtotal: config.initialSubtotal,
            base_shipping_fee: config.initialRates['fast']?.base_fee || 22000,
            xtra_discount: config.initialRates['fast']?.discount || 0,
            voucher_shipping_discount: 0,
            total_shipping_discount: config.initialRates['fast']?.discount || 0,
            final_shipping_fee: config.initialRates['fast']?.final_fee || 22000,
            order_discount: 0,
            grand_total: config.initialSubtotal + (config.initialRates['fast']?.final_fee || 22000),
        },

        init() {
            this.recalculate();
        },

        formatMoney(amount) {
            return new Intl.NumberFormat('vi-VN').format(Math.round(amount || 0));
        },

        async recalculate() {
            this.calculating = true;
            try {
                const res = await fetch(config.calculateUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': config.csrfToken
                    },
                    body: JSON.stringify({
                        province: this.province,
                        shipping_service: this.shippingService,
                        coupon_code: this.couponCode,
                        shipping_coupon_code: this.shippingCouponCode,
                    })
                });

                const data = await res.json();
                if (data.success) {
                    this.rates = data.shipping_rates;
                    this.breakdown = data;
                    this.orderCouponValid = data.order_coupon_valid;
                    this.orderCouponMsg = data.order_coupon_message || '';
                    this.shippingCouponValid = data.shipping_coupon_valid;
                    this.shippingCouponMsg = data.shipping_coupon_message || '';

                    // If previously selected service is not available in new province (e.g. instant)
                    if (!this.rates[this.shippingService]) {
                        this.shippingService = 'fast';
                    }
                }
            } catch (e) {
                console.error('Error recalculating checkout rates:', e);
            } finally {
                this.calculating = false;
            }
        },

        applyShopVoucher(code) {
            if (!code || !code.trim()) return;
            this.couponCode = code.trim().toUpperCase();
            this.inputCoupon = this.couponCode;
            this.recalculate();
        },

        removeShopVoucher() {
            this.couponCode = '';
            this.inputCoupon = '';
            this.orderCouponMsg = '';
            this.recalculate();
        },

        applyShipVoucher(code) {
            if (!code || !code.trim()) return;
            this.shippingCouponCode = code.trim().toUpperCase();
            this.inputShippingCoupon = this.shippingCouponCode;
            this.recalculate();
        },

        removeShipVoucher() {
            this.shippingCouponCode = '';
            this.inputShippingCoupon = '';
            this.shippingCouponMsg = '';
            this.recalculate();
        }
    }
}
</script>
@endpush
@endsection
