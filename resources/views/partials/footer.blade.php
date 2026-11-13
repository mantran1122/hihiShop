<!-- resources/views/partials/footer.blade.php -->
<footer class="bg-slate-900 text-slate-400 mt-20 border-t border-slate-800">
    <!-- Value Propositions / Trust Badges -->
    <div class="border-b border-slate-800/80 bg-slate-900/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 sm:gap-8">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-white text-sm font-bold">100% Chính Hãng</h4>
                        <p class="text-xs text-slate-400 mt-0.5">Cam kết nguồn gốc xuất xứ</p>
                    </div>
                </div>

                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-sky-500/10 border border-sky-500/20 text-sky-400 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    </div>
                    <div>
                        <h4 class="text-white text-sm font-bold">Giao Hàng Miễn Phí</h4>
                        <p class="text-xs text-slate-400 mt-0.5">Đơn COD toàn quốc</p>
                    </div>
                </div>

                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </div>
                    <div>
                        <h4 class="text-white text-sm font-bold">Đổi Trả 7 Ngày</h4>
                        <p class="text-xs text-slate-400 mt-0.5">Nhanh chóng & linh hoạt</p>
                    </div>
                </div>

                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-white text-sm font-bold">Hỗ Trợ 24/7</h4>
                        <p class="text-xs text-slate-400 mt-0.5">Tư vấn tận tâm, chu đáo</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Footer Links -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            
            <!-- Col 1: About HIHI Shop -->
            <div>
                <a href="{{ route('home') }}" class="flex items-center gap-2 text-2xl font-black tracking-tight text-white mb-4">
                    <span class="w-8 h-8 rounded-xl bg-gradient-to-tr from-rose-600 to-rose-400 text-white flex items-center justify-center font-black text-sm">
                        H
                    </span>
                    <span>HIHI<span class="text-rose-500">Shop</span></span>
                </a>
                <p class="text-sm text-slate-400 leading-relaxed mb-4">
                    Thế giới phụ kiện & thời trang cao cấp. Trải nghiệm mua sắm đẳng cấp với các bộ sưu tập nước hoa, giày, mắt kính và đồng hồ độc quyền.
                </p>
                <div class="flex items-center gap-3">
                    <a href="#" class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-rose-600 text-slate-300 hover:text-white flex items-center justify-center transition" aria-label="Facebook">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
                    </a>
                    <a href="#" class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-rose-600 text-slate-300 hover:text-white flex items-center justify-center transition" aria-label="Instagram">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                    <a href="#" class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-rose-600 text-slate-300 hover:text-white flex items-center justify-center transition" aria-label="Youtube">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    </a>
                </div>
            </div>

            <!-- Col 2: Categories -->
            <div>
                <h4 class="text-white text-sm font-bold uppercase tracking-wider mb-4">Danh Mục Sản Phẩm</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('category.show', 'nuoc-hoa') }}" class="hover:text-white transition">Nước hoa cao cấp</a></li>
                    <li><a href="{{ route('category.show', 'giay') }}" class="hover:text-white transition">Giày sneaker & công sở</a></li>
                    <li><a href="{{ route('category.show', 'mat-kinh') }}" class="hover:text-white transition">Mắt kính thời trang</a></li>
                    <li><a href="{{ route('category.show', 'dong-ho') }}" class="hover:text-white transition">Đồng hồ chính hãng</a></li>
                    <li><a href="{{ route('product.index') }}" class="hover:text-white transition">Tất cả sản phẩm</a></li>
                </ul>
            </div>

            <!-- Col 3: Customer Policy -->
            <div>
                <h4 class="text-white text-sm font-bold uppercase tracking-wider mb-4">Hỗ Trợ Khách Hàng</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="#" class="hover:text-white transition">Chính sách vận chuyển & COD</a></li>
                    <li><a href="#" class="hover:text-white transition">Chính sách đổi trả trong 7 ngày</a></li>
                    <li><a href="#" class="hover:text-white transition">Hướng dẫn chọn size chuẩn</a></li>
                    <li><a href="#" class="hover:text-white transition">Quy định bảo hành sản phẩm</a></li>
                    <li><a href="#" class="hover:text-white transition">Bảo mật thông tin khách hàng</a></li>
                </ul>
            </div>

            <!-- Col 4: Contact -->
            <div>
                <h4 class="text-white text-sm font-bold uppercase tracking-wider mb-4">Thông Tin Liên Hệ</h4>
                <div class="space-y-3 text-sm">
                    <p class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>123 Đường Mỹ Phẩm, Quận 1, TP. Hồ Chí Minh</span>
                    </p>
                    <p class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span>Hotline: <a href="tel:{{ $siteHotlineTel }}" class="text-white font-bold hover:text-rose-400 transition">{{ $siteHotline }}</a></span>
                    </p>
                    <p class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span>Email: support@hihishop.com</span>
                    </p>
                </div>
            </div>

        </div>
    </div>

    <!-- Copyright Bar -->
    <div class="border-t border-slate-800 py-6 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p>&copy; {{ date('Y') }} HIHI-SHOP. Nền tảng thương mại điện tử phụ kiện & thời trang.</p>
            <div class="flex items-center gap-4 text-slate-400">
                <span class="text-slate-600">Thanh toán an toàn:</span>
                <span class="px-2 py-1 rounded bg-slate-800 text-[11px] font-bold text-slate-300">Tiền mặt COD</span>
                <span class="px-2 py-1 rounded bg-slate-800 text-[11px] font-bold text-slate-300">Chính hãng</span>
            </div>
        </div>
    </div>
</footer>
