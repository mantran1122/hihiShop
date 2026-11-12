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
        <span class="text-slate-900 font-bold">Tìm kiếm</span>
    </nav>

    <!-- Header & Results Count -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 mb-8 shadow-sm">
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
            Kết quả tìm kiếm cho: <span class="text-rose-600">"{{ $q }}"</span>
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">
            Tìm thấy <strong>{{ $products->total() }}</strong> sản phẩm phù hợp
        </p>
    </div>

    @if($products->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
            @foreach($products as $p)
                @php 
                    $v = $p->bienTheMacDinh ?? $p->bienThes->first(); 
                    $price = number_format($p->price, 0, '.', '.');
                @endphp
                <article class="group relative bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-sm hover:shadow-card-hover hover:border-rose-200 transition-all duration-300 flex flex-col product-card">
                    <a href="{{ route('product.show', $p->slug) }}" class="relative aspect-square bg-slate-50 flex items-center justify-center p-4 overflow-hidden block" aria-label="{{ $p->name }}">
                        <img loading="lazy" src="{{ $p->image_url }}" alt="{{ $p->name }}" class="max-h-full max-w-full object-contain product-img"/>
                    </a>

                    <div class="p-4 sm:p-5 flex flex-col flex-grow">
                        <a href="{{ route('product.show', $p->slug) }}" class="font-bold text-slate-900 text-sm sm:text-base line-clamp-2 hover:text-rose-600 transition-colors leading-snug mb-3">
                            {{ $p->name }}
                        </a>

                        <div class="mt-auto pt-2 flex flex-col gap-3">
                            <span class="text-base sm:text-lg font-black text-rose-600">
                                {{ $price }}đ
                            </span>

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
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">Không tìm thấy sản phẩm</h3>
            <p class="text-sm text-slate-500 mb-6">Không tìm thấy kết quả nào với từ khóa "{{ $q }}". Vui lòng thử tìm với từ khóa khác.</p>
            <a href="{{ route('home') }}" class="btn-primary">
                Về trang chủ
            </a>
        </div>
    @endif
</div>
@endsection
