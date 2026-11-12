@extends('layouts.shop')

@section('content')
<div class="py-4">
    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-2 text-xs sm:text-sm text-slate-500 mb-6 flex-wrap" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-rose-600 transition flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Trang chủ
        </a>
        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        
        @if($p->danhMuc)
            <a href="{{ route('category.show', $p->danhMuc->slug) }}" class="hover:text-rose-600 transition">
                {{ $p->danhMuc->name }}
            </a>
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        @endif
        
        <span class="text-slate-900 font-bold truncate max-w-xs sm:max-w-md">
            {{ $p->name }}
        </span>
    </nav>

    <!-- Main Detail Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-10 shadow-sm">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
            
            <!-- Left Col: Product Gallery (5 cols) -->
            <div class="lg:col-span-5 flex flex-col gap-4">
                <div class="w-full aspect-square bg-slate-50 rounded-2xl border border-slate-200/80 flex items-center justify-center p-6 overflow-hidden relative group">
                    <img id="main-product-image" src="{{ $p->image_url }}" alt="{{ $p->name }}" class="max-h-full max-w-full object-contain transition-transform duration-500 group-hover:scale-105">
                </div>

                @if($p->anh && $p->anh->count() > 1)
                    <div class="flex gap-3 overflow-x-auto no-scrollbar py-1">
                        <!-- Main thumbnail -->
                        <button type="button" 
                                onclick="changeMainImage('{{ $p->image_url }}', this)" 
                                class="thumb-btn w-16 h-16 sm:w-20 sm:h-20 bg-slate-50 rounded-xl border-2 border-rose-600 p-1 flex items-center justify-center shrink-0 cursor-pointer overflow-hidden transition">
                            <img src="{{ $p->image_url }}" alt="" class="max-h-full max-w-full object-contain">
                        </button>

                        @foreach($p->anh as $img)
                            <button type="button" 
                                    onclick="changeMainImage('{{ asset($img->path) }}', this)" 
                                    class="thumb-btn w-16 h-16 sm:w-20 sm:h-20 bg-slate-50 rounded-xl border-2 border-slate-200 hover:border-slate-400 p-1 flex items-center justify-center shrink-0 cursor-pointer overflow-hidden transition">
                                <img src="{{ asset($img->path) }}" alt="" class="max-h-full max-w-full object-contain">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Right Col: Product Information & Form (7 cols) -->
            <div class="lg:col-span-7 flex flex-col">
                @if($p->danhMuc)
                    <span class="text-xs font-bold text-rose-600 uppercase tracking-widest mb-1.5">
                        {{ $p->danhMuc->name }}
                    </span>
                @endif

                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 leading-snug tracking-tight mb-4">
                    {{ $p->name }}
                </h1>

                <!-- Rating Summary -->
                <div class="flex items-center gap-3 mb-5 flex-wrap">
                    @include('partials.stars', ['rating' => $reviewStats['average'], 'size' => 'w-4 h-4'])
                    <span class="text-sm font-black text-slate-900">{{ number_format($reviewStats['average'], 1) }}/5</span>
                    <a href="#reviews" class="text-xs font-semibold text-slate-500 hover:text-rose-600 transition underline decoration-dotted underline-offset-4">
                        {{ $reviewStats['count'] }} đánh giá
                    </a>
                </div>

                <!-- Price & Stock Status -->
                <div class="flex items-center gap-4 mb-6 pb-6 border-b border-slate-100 flex-wrap">
                    <span id="variant-price" class="text-3xl sm:text-4xl font-black text-rose-600">
                        {{ number_format($p->price, 0, '.', '.') }}đ
                    </span>
                    <span id="stock-badge" class="badge-success text-xs font-bold px-3 py-1">
                        ● Còn hàng
                    </span>

                    <!-- Wishlist Toggle -->
                    @auth
                        <form method="POST" action="{{ route('wishlist.toggle', $p) }}" class="ml-auto m-0">
                            @csrf
                            <button type="submit" title="{{ $isWished ? 'Bỏ khỏi danh sách yêu thích' : 'Thêm vào danh sách yêu thích' }}"
                                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border text-xs font-bold transition {{ $isWished ? 'bg-rose-50 border-rose-200 text-rose-600' : 'bg-white border-slate-200 text-slate-600 hover:border-rose-200 hover:text-rose-600' }}">
                                <svg class="w-4 h-4" fill="{{ $isWished ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                                <span>{{ $isWished ? 'Đã yêu thích' : 'Yêu thích' }}</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="ml-auto inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:border-rose-200 hover:text-rose-600 transition" title="Đăng nhập để lưu sản phẩm yêu thích">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            <span>Yêu thích</span>
                        </a>
                    @endauth
                </div>

                @if($p->short_desc)
                    <div class="text-slate-600 text-sm sm:text-base leading-relaxed mb-6">
                        {{ $p->short_desc }}
                    </div>
                @endif

                <!-- Purchase Form -->
                <form method="post" action="{{ route('cart.add') }}" class="space-y-6">
                    @csrf
                    
                    <!-- Variant Selector -->
                    <div>
                        <label for="variant-select" class="form-label flex items-center justify-between">
                            <span>Chọn phiên bản / kích cỡ:</span>
                            <span class="text-xs text-slate-400 font-normal">({{ $p->bienThes->count() }} lựa chọn)</span>
                        </label>
                        <select id="variant-select" name="variant_id" required class="form-select font-semibold">
                            @foreach($p->bienThes as $v)
                                <option value="{{ $v->id }}" 
                                        data-price="{{ $v->price }}" 
                                        data-stock="{{ $v->stock_qty }}"
                                        data-formatted-price="{{ number_format($v->price, 0, '.', '.') }}đ">
                                    {{ $v->sku }} — {{ number_format($v->price, 0, '.', '.') }}đ (Còn: {{ $v->stock_qty }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Quantity & Action Buttons -->
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-end gap-4">
                        <div class="w-full sm:w-36">
                            <label for="qty-input" class="form-label">Số lượng:</label>
                            <div class="flex items-center border border-slate-200 rounded-xl bg-white overflow-hidden shadow-sm">
                                <button type="button" onclick="stepQty(-1)" class="w-10 h-11 flex items-center justify-center text-slate-500 hover:text-slate-900 hover:bg-slate-50 font-bold transition">
                                    -
                                </button>
                                <input id="qty-input" type="number" name="qty" value="1" min="1" max="100" class="w-full text-center border-none p-0 focus:ring-0 font-bold text-slate-900 text-sm">
                                <button type="button" onclick="stepQty(1)" class="w-10 h-11 flex items-center justify-center text-slate-500 hover:text-slate-900 hover:bg-slate-50 font-bold transition">
                                    +
                                </button>
                            </div>
                        </div>

                        <div class="flex-1">
                            @auth
                                <button id="add-to-cart-btn" class="btn-primary w-full py-3 text-base rounded-xl font-bold flex items-center justify-center gap-2" type="submit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    <span>Thêm vào giỏ hàng</span>
                                </button>
                            @else
                                <button id="add-to-cart-btn" class="btn-primary w-full py-3 text-base rounded-xl font-bold flex items-center justify-center gap-2" type="submit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                    <span>Đăng nhập để thêm vào giỏ</span>
                                </button>
                            @endauth
                        </div>
                    </div>
                </form>

                <!-- Value Guarantees -->
                <div class="mt-8 pt-6 border-t border-slate-100 grid grid-cols-2 gap-3.5 text-xs text-slate-600 font-medium">
                    <div class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>100% Hàng chính hãng</span>
                    </div>
                    <div class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                        <svg class="w-4 h-4 text-sky-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        <span>Giao hàng COD toàn quốc</span>
                    </div>
                    <div class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Đổi trả linh hoạt 7 ngày</span>
                    </div>
                    <div class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                        <svg class="w-4 h-4 text-purple-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Bảo hành theo quy định</span>
                    </div>
                </div>

                <!-- Description Section -->
                @if($p->description)
                    <div class="mt-8 pt-8 border-t border-slate-100">
                        <h2 class="text-base sm:text-lg font-bold text-slate-900 mb-3">Mô tả chi tiết sản phẩm</h2>
                        <div class="text-sm sm:text-base text-slate-600 leading-relaxed space-y-2 whitespace-pre-line">
                            {!! nl2br(e($p->description)) !!}
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- Reviews & Ratings -->
    <section id="reviews" class="mt-8 grid grid-cols-1 lg:grid-cols-12 gap-6 scroll-mt-28">
        <!-- Summary -->
        <div class="lg:col-span-4">
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
                <h2 class="text-base font-black text-slate-900">Đánh giá sản phẩm</h2>

                <div class="mt-4 flex items-end gap-3">
                    <span class="text-5xl font-black text-slate-900 leading-none">{{ number_format($reviewStats['average'], 1) }}</span>
                    <div class="pb-1">
                        @include('partials.stars', ['rating' => $reviewStats['average']])
                        <p class="text-xs text-slate-500 mt-1">{{ $reviewStats['count'] }} đánh giá</p>
                    </div>
                </div>

                <div class="mt-5 space-y-2">
                    @foreach($reviewStats['distribution'] as $star => $starCount)
                        @php $percent = $reviewStats['count'] > 0 ? round($starCount / $reviewStats['count'] * 100) : 0; @endphp
                        <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                            <span class="w-9 shrink-0">{{ $star }} sao</span>
                            <div class="flex-1 h-2 rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-full bg-amber-400 rounded-full transition-all" style="width: {{ $percent }}%"></div>
                            </div>
                            <span class="w-6 text-right tabular-nums">{{ $starCount }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Form + list -->
        <div class="lg:col-span-8 space-y-6">
            @auth
                <form method="POST" action="{{ route('review.store', $p) }}" class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
                    @csrf
                    <div class="flex items-center justify-between gap-3 flex-wrap">
                        <h3 class="text-base font-black text-slate-900">
                            {{ $myReview ? 'Cập nhật đánh giá của bạn' : 'Viết đánh giá' }}
                        </h3>
                        <span class="text-xs text-slate-400">Cảm nhận của bạn giúp khách hàng khác chọn đúng sản phẩm</span>
                    </div>

                    <div class="mt-4" x-data="{ rating: {{ (int) old('rating', $myReview->rating ?? 5) }} }">
                        <span class="form-label">Chấm điểm <span class="text-rose-600">*</span></span>
                        <div class="flex items-center gap-1">
                            <template x-for="i in 5" :key="i">
                                <button type="button" @click="rating = i" :aria-label="'Chọn ' + i + ' sao'" class="p-0.5 transition-transform hover:scale-110 cursor-pointer">
                                    <svg class="w-8 h-8 transition-colors" :class="i <= rating ? 'text-amber-400' : 'text-slate-300'" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </button>
                            </template>
                            <span class="ml-2 text-sm font-bold text-slate-500" x-text="rating + '/5'"></span>
                        </div>
                        <input type="hidden" name="rating" :value="rating">
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-4">
                        <div>
                            <label class="form-label" for="review-title">Tiêu đề</label>
                            <input id="review-title" name="title" type="text" maxlength="120"
                                   value="{{ old('title', $myReview->title ?? '') }}"
                                   placeholder="Ví dụ: Hàng đẹp, đóng gói cẩn thận"
                                   class="form-input">
                        </div>
                        <div>
                            <label class="form-label" for="review-content">Nội dung</label>
                            <textarea id="review-content" name="content" rows="4" maxlength="2000"
                                      placeholder="Cảm nhận của bạn về sản phẩm..."
                                      class="form-input resize-y">{{ old('content', $myReview->content ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center gap-3 flex-wrap">
                        <button type="submit" class="btn-primary px-5 py-2.5 text-sm font-bold">
                            {{ $myReview ? 'Cập nhật đánh giá' : 'Gửi đánh giá' }}
                        </button>
                        @if($myReview)
                            <span class="text-xs text-slate-400">Bạn đã đánh giá {{ $myReview->stars }} sao</span>
                        @endif
                    </div>
                </form>
            @else
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm text-center">
                    <p class="text-sm font-semibold text-slate-700">Bạn cần đăng nhập để viết đánh giá sản phẩm.</p>
                    <a href="{{ route('login') }}" class="btn-primary inline-flex mt-4 px-5 py-2.5 text-sm font-bold">Đăng nhập</a>
                </div>
            @endauth

            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
                <h3 class="text-base font-black text-slate-900">Khách hàng nhận xét ({{ $reviewStats['count'] }})</h3>

                <div class="mt-4 divide-y divide-slate-100">
                    @forelse($reviews as $review)
                        <article class="py-4 first:pt-0 last:pb-0">
                            <div class="flex items-start gap-3">
                                <span class="w-9 h-9 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-xs uppercase shrink-0">
                                    {{ mb_substr($review->user->name ?? 'K', 0, 1) }}
                                </span>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-sm font-bold text-slate-900">{{ $review->user->name ?? 'Khách hàng' }}</span>
                                        @include('partials.stars', ['rating' => $review->stars, 'size' => 'w-3.5 h-3.5'])
                                        <span class="text-[11px] text-slate-400">{{ $review->created_at?->format('d/m/Y') }}</span>

                                        @if(auth()->check() && (auth()->id() === $review->user_id || auth()->user()->hasRole('Admin')))
                                            <form method="POST" action="{{ route('review.destroy', $review) }}" class="ml-auto m-0" onsubmit="return confirm('Xoá đánh giá này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-[11px] font-bold text-slate-400 hover:text-rose-600 transition cursor-pointer">Xoá</button>
                                            </form>
                                        @endif
                                    </div>

                                    @if($review->title)
                                        <h4 class="mt-1.5 text-sm font-bold text-slate-800">{{ $review->title }}</h4>
                                    @endif
                                    @if($review->content)
                                        <p class="mt-1 text-sm text-slate-600 leading-relaxed whitespace-pre-line">{{ $review->content }}</p>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @empty
                        <p class="py-6 text-sm text-slate-500 text-center">Chưa có đánh giá nào cho sản phẩm này. Hãy là người đầu tiên!</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    function changeMainImage(src, btn) {
        document.getElementById('main-product-image').src = src;
        document.querySelectorAll('.thumb-btn').forEach(b => {
            b.classList.remove('border-rose-600');
            b.classList.add('border-slate-200');
        });
        btn.classList.add('border-rose-600');
        btn.classList.remove('border-slate-200');
    }

    function stepQty(delta) {
        const input = document.getElementById('qty-input');
        const min = parseInt(input.min) || 1;
        const max = parseInt(input.max) || 100;
        let val = parseInt(input.value) || 1;
        val += delta;
        if (val < min) val = min;
        if (val > max) val = max;
        input.value = val;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const variantSelect = document.getElementById('variant-select');
        const priceDisplay = document.getElementById('variant-price');
        const stockBadge = document.getElementById('stock-badge');
        const qtyInput = document.getElementById('qty-input');
        const addBtn = document.getElementById('add-to-cart-btn');

        function updateVariantInfo() {
            if (!variantSelect) return;
            const selected = variantSelect.options[variantSelect.selectedIndex];
            if (!selected) return;

            const formattedPrice = selected.getAttribute('data-formatted-price');
            const stock = parseInt(selected.getAttribute('data-stock'), 10) || 0;

            if (formattedPrice) {
                priceDisplay.textContent = formattedPrice;
            }

            if (stock > 0) {
                stockBadge.textContent = '● Còn ' + stock + ' sản phẩm';
                stockBadge.className = 'badge-success text-xs font-bold px-3 py-1';
                qtyInput.max = stock;
                addBtn.disabled = false;
                addBtn.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg><span>Thêm vào giỏ hàng</span>`;
            } else {
                stockBadge.textContent = '● Tạm hết hàng';
                stockBadge.className = 'badge-danger text-xs font-bold px-3 py-1';
                qtyInput.max = 0;
                addBtn.disabled = true;
                addBtn.innerHTML = `<span>Tạm hết hàng</span>`;
            }
        }

        if (variantSelect) {
            variantSelect.addEventListener('change', updateVariantInfo);
            updateVariantInfo();
        }
    });
</script>
@endsection
