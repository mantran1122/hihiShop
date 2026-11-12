{{-- resources/views/home.blade.php --}}
@extends('layouts.shop')

@section('body_class', 'home-theme')

@section('content')
    @include('partials.hero')

    <!-- Featured Categories Section -->
    <section class="mt-12 sm:mt-16">
        <div class="flex items-end justify-between mb-6">
            <div>
                <span class="text-xs font-bold text-rose-600 uppercase tracking-widest">Bộ Sưu Tập</span>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mt-1">Danh mục nổi bật</h2>
            </div>
            <a href="{{ route('category.index') }}" class="text-sm font-bold text-rose-600 hover:text-rose-700 flex items-center gap-1 group transition">
                <span>Xem tất cả</span>
                <span class="group-hover:translate-x-1 transition-transform">→</span>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6" id="category-grid">
            @foreach($categories as $c)
                <a href="{{ route('category.show', $c->slug) }}" class="group relative bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-sm hover:shadow-card-hover hover:border-rose-200 transition-all duration-300 block">
                    <div class="aspect-video sm:aspect-[4/3] bg-slate-100 flex items-center justify-center overflow-hidden relative">
                        <img loading="lazy" src="{{ $c->image_url }}" alt="{{ $c->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"/>
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 via-transparent to-transparent opacity-60 group-hover:opacity-40 transition-opacity"></div>
                    </div>
                    <div class="p-4 flex items-center justify-between bg-white">
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm sm:text-base group-hover:text-rose-600 transition-colors">{{ $c->name }}</h3>
                            <span class="text-xs text-slate-500 font-medium">Khám phá ngay</span>
                        </div>
                        <span class="w-8 h-8 rounded-full bg-slate-50 group-hover:bg-rose-50 text-slate-400 group-hover:text-rose-600 flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <!-- Latest Products Section -->
    <section class="mt-14 sm:mt-20">
        <div class="flex items-end justify-between mb-6">
            <div>
                <span class="text-xs font-bold text-rose-600 uppercase tracking-widest">Sản Phẩm Đang Hot</span>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mt-1">Sản phẩm mới nhất</h2>
            </div>
            <a href="{{ route('product.index') }}" class="text-sm font-bold text-rose-600 hover:text-rose-700 flex items-center gap-1 group transition">
                <span>Xem tất cả</span>
                <span class="group-hover:translate-x-1 transition-transform">→</span>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6" id="product-grid">
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
                    
                    <!-- Media Container -->
                    <a href="{{ route('product.show', $p->slug) }}" class="relative aspect-square bg-slate-50 flex items-center justify-center p-4 overflow-hidden block" aria-label="{{ $p->name }}">
                        <img loading="lazy" src="{{ $p->image_url }}" alt="{{ $p->name }}" class="max-h-full max-w-full object-contain product-img"/>
                        
                        <!-- Badges -->
                        <div class="absolute top-3 left-3 flex flex-col gap-1.5 z-10">
                            <span class="px-2.5 py-1 text-[11px] font-extrabold uppercase tracking-wider text-white bg-slate-900/90 backdrop-blur-sm rounded-lg shadow-sm">
                                Mới
                            </span>
                            @if($hasDiscount)
                                <span class="px-2 py-0.5 text-[11px] font-black text-rose-700 bg-rose-100 rounded-lg shadow-sm">
                                    -{{ $percent }}%
                                </span>
                            @endif
                        </div>
                    </a>

                    <!-- Card Body -->
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
    </section>
@endsection
