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
        <span class="text-slate-900 font-bold">{{ $cat->name }}</span>
    </nav>

    <!-- Header & Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 mb-8 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                {{ $cat->name }}
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Hiển thị <strong>{{ $products->total() }}</strong> sản phẩm chất lượng cao
            </p>
        </div>

        <!-- Sort Form -->
        <form method="get" action="{{ route('category.show', $cat->slug) }}" class="flex items-center gap-3">
            <label for="sort" class="text-xs font-bold text-slate-600 whitespace-nowrap">Sắp xếp:</label>
            <select name="sort" id="sort" onchange="this.form.submit()" class="form-select text-xs sm:text-sm py-2 pl-3 pr-8 rounded-xl font-medium">
                <option value="">Mới nhất</option>
                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá tăng dần</option>
                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá giảm dần</option>
            </select>
        </form>
    </div>

    <!-- Product Grid or Empty State -->
    @if($products->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
            @foreach($products as $p)
                @php 
                    $v = $p->bienTheMacDinh ?? $p->bienThes->first(); 
                    $price = number_format($p->price, 0, '.', '.');
                    $hasDiscount = !empty($v?->compare_price) && ($v->compare_price > ($v->price ?? 0));
                    if ($hasDiscount) {
                        $percent = round((($v->compare_price - $v->price) / $v->compare_price) * 100);
                    }
                @endphp
                <article class="group relative bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-sm hover:shadow-card-hover hover:border-rose-200 transition-all duration-300 flex flex-col product-card">
                    <a href="{{ route('product.show', $p->slug) }}" class="relative aspect-square bg-slate-50 flex items-center justify-center p-4 overflow-hidden block" aria-label="{{ $p->name }}">
                        <img loading="lazy" src="{{ $p->image_url }}" alt="{{ $p->name }}" class="max-h-full max-w-full object-contain product-img"/>
                        
                        @if($hasDiscount)
                            <div class="absolute top-3 left-3 z-10">
                                <span class="px-2 py-0.5 text-[11px] font-black text-rose-700 bg-rose-100 rounded-lg shadow-sm">
                                    -{{ $percent }}%
                                </span>
                            </div>
                        @endif
                    </a>

                    <div class="p-4 sm:p-5 flex flex-col flex-grow">
                        <a href="{{ route('product.show', $p->slug) }}" class="font-bold text-slate-900 text-sm sm:text-base line-clamp-2 hover:text-rose-600 transition-colors leading-snug mb-3">
                            {{ $p->name }}
                        </a>

                        <div class="mt-auto pt-2 flex flex-col gap-3">
                            <div class="flex items-baseline gap-2 flex-wrap">
                                <span class="text-base sm:text-lg font-black text-rose-600">
                                    {{ $price }}đ
                                </span>
                                @if($hasDiscount)
                                    <span class="text-xs text-slate-400 line-through">
                                        {{ number_format($v->compare_price, 0, '.', '.') }}đ
                                    </span>
                                @endif
                            </div>

                            <a href="{{ route('product.show', $p->slug) }}" class="btn-primary text-xs sm:text-sm py-2 sm:py-2.5 w-full rounded-xl">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-10">
            {{ $products->links() }}
        </div>
    @else
        <div class="bg-white rounded-3xl border border-slate-200/80 p-12 text-center max-w-lg mx-auto shadow-sm">
            <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">Chưa có sản phẩm</h3>
            <p class="text-sm text-slate-500 mb-6">Hiện chưa có sản phẩm nào trong danh mục này. Vui lòng quay lại sau.</p>
            <a href="{{ route('home') }}" class="btn-primary">
                Về trang chủ
            </a>
        </div>
    @endif
</div>
@endsection
