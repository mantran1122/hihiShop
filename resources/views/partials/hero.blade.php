<!-- resources/views/partials/hero.blade.php -->
<section class="relative w-full rounded-3xl overflow-hidden shadow-lg bg-slate-900 my-4">
    <div class="relative h-[340px] sm:h-[420px] lg:h-[480px] w-full overflow-hidden">
        <!-- Video Background Layer -->
        <video class="absolute inset-0 w-full h-full object-cover filter blur-md brightness-50 scale-105 pointer-events-none" autoplay muted loop playsinline preload="metadata">
            <source src="{{ asset('images/images_background/demo1.mp4') }}" type="video/mp4">
        </video>

        <!-- Main Video Layer -->
        <video class="absolute inset-0 w-full h-full object-cover sm:object-contain mix-blend-screen pointer-events-none opacity-90" autoplay muted loop playsinline preload="metadata">
            <source src="{{ asset('images/images_background/demo1.mp4') }}" type="video/mp4">
        </video>

        <!-- Dark Gradient Overlay for optimal readability -->
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-900/40 to-slate-950/30 z-10"></div>

        <!-- Content & CTA -->
        <div class="relative z-20 h-full max-w-4xl mx-auto px-6 flex flex-col items-center justify-center text-center text-white space-y-4 sm:space-y-6">
            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold tracking-wide uppercase bg-rose-500/20 text-rose-300 border border-rose-500/30 backdrop-blur-md">
                Bộ Sưu Tập Mới 2026
            </span>

            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight text-white drop-shadow-md">
                Nâng Tầm Phong Cách <br class="hidden sm:inline">
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-rose-400 via-rose-300 to-amber-200">
                    Đẳng Cấp Mỗi Ngày
                </span>
            </h1>

            <p class="text-sm sm:text-base lg:text-lg text-slate-200 max-w-2xl font-medium drop-shadow">
                Khám phá thế giới nước hoa, giày sneaker, mắt kính và đồng hồ chính hãng với trải nghiệm mua sắm tiện lợi nhất.
            </p>

            <div class="flex items-center gap-3 pt-2">
                <a href="{{ route('product.index') }}" class="btn-primary text-sm sm:text-base px-6 sm:px-8 py-3 rounded-2xl shadow-lg shadow-rose-600/30 hover:scale-105 transition-all">
                    Khám phá ngay →
                </a>
                <a href="{{ route('category.show', 'nuoc-hoa') }}" class="px-5 py-3 rounded-2xl text-sm sm:text-base font-semibold text-white bg-white/10 hover:bg-white/20 border border-white/20 backdrop-blur-md transition-all">
                    Xem Deal Hot
                </a>
            </div>
        </div>
    </div>
</section>
