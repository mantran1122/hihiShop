@extends('layouts.shop')

@section('content')
<div class="py-4">
    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-2 text-xs sm:text-sm text-slate-500 mb-6 flex-wrap" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-rose-600 transition">Trang chủ</a>
        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-900 font-bold">Sản phẩm yêu thích</span>
    </nav>

    <div class="flex items-end justify-between gap-4 mb-6 flex-wrap">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <svg class="w-7 h-7 text-rose-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                Sản phẩm yêu thích
            </h1>
            <p class="text-sm text-slate-500 mt-1.5">
                {{ $items->count() }} sản phẩm bạn đã lưu để xem lại sau.
            </p>
        </div>

        <a href="{{ route('product.index') }}" class="btn-secondary text-sm">
            Tiếp tục mua sắm
        </a>
    </div>

    @if($items->isEmpty())
        <div class="bg-white rounded-3xl border border-slate-200/80 p-10 sm:p-16 text-center shadow-sm">
            <div class="w-20 h-20 mx-auto rounded-full bg-rose-50 text-rose-500 flex items-center justify-center mb-5">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </div>
            <h2 class="text-lg font-black text-slate-900">Danh sách yêu thích đang trống</h2>
            <p class="text-sm text-slate-500 mt-2 max-w-md mx-auto">
                Bấm vào biểu tượng trái tim ở trang chi tiết sản phẩm để lưu lại những món đồ bạn thích.
            </p>
            <a href="{{ route('product.index') }}" class="btn-primary inline-flex mt-6 px-6 py-2.5 text-sm font-bold">
                Khám phá sản phẩm
            </a>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
            @foreach($items as $item)
                @php $p = $item->sanPham; @endphp
                <article class="group relative bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-sm hover:shadow-lg hover:border-rose-200 transition-all duration-300 flex flex-col">
                    <a href="{{ route('product.show', $p->slug) }}" class="relative aspect-square bg-slate-50 flex items-center justify-center p-4 overflow-hidden block">
                        <img loading="lazy" src="{{ $p->image_url }}" alt="{{ $p->name }}" class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-500"/>
                    </a>

                    <!-- Nút bỏ yêu thích -->
                    <form method="POST" action="{{ route('wishlist.toggle', $p) }}" class="absolute top-3 right-3 z-10 m-0" title="Bỏ khỏi danh sách yêu thích">
                        @csrf
                        <button type="submit" class="w-9 h-9 rounded-full bg-white/90 backdrop-blur border border-rose-200 text-rose-600 flex items-center justify-center shadow-sm hover:bg-rose-50 transition cursor-pointer">
                            <svg class="w-4.5 h-4.5" fill="currentColor" viewBox="0 0 24 24" style="width:1.1rem;height:1.1rem;">
                                <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>
                    </form>

                    <div class="p-4 sm:p-5 flex flex-col flex-grow">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-rose-600 mb-1">
                            {{ $p->danhMuc->name ?? 'Sản phẩm' }}
                        </span>
                        <a href="{{ route('product.show', $p->slug) }}" class="font-bold text-slate-900 text-sm sm:text-base line-clamp-2 hover:text-rose-600 transition-colors leading-snug mb-3">
                            {{ $p->name }}
                        </a>

                        <div class="mt-auto pt-2 flex flex-col gap-3">
                            <span class="text-base sm:text-lg font-black text-rose-600">
                                {{ number_format($p->price, 0, '.', '.') }}đ
                            </span>
                            <a href="{{ route('product.show', $p->slug) }}" class="btn-primary text-xs sm:text-sm py-2 sm:py-2.5 w-full rounded-xl">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</div>
@endsection
