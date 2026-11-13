@extends('layouts.admin')

@section('title', 'Cài đặt hiển thị')
@section('page_title', 'Cài Đặt Hiển Thị')

@section('content')
<div class="max-w-3xl space-y-6">

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 shadow-sm">
            <ul class="list-disc list-inside text-sm space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="p-4 rounded-2xl bg-sky-50 border border-sky-200 text-sky-800 text-sm leading-relaxed">
        Các thông tin này hiển thị ở thanh trên cùng và chân trang của mọi trang. Thay đổi có hiệu lực ngay, không cần deploy.
    </div>

    <form method="POST" action="{{ route('admin.settings.site.update') }}" class="space-y-6">
        @csrf

        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-5">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Thông báo đầu trang</h2>

            <div>
                <label for="announcement_text" class="form-label">Nội dung thông báo</label>
                <input id="announcement_text" name="announcement_text" type="text" maxlength="200"
                       value="{{ old('announcement_text', $settings['announcement_text']) }}"
                       placeholder="Ví dụ: Miễn phí giao hàng toàn quốc cho đơn từ 250K"
                       class="form-input">
                <p class="text-[11px] text-slate-400 mt-1.5">Tối đa 200 ký tự. Để trống sẽ ẩn thanh thông báo.</p>
            </div>

            <label class="flex items-center gap-2.5 text-sm font-semibold text-slate-700 cursor-pointer">
                <input type="hidden" name="announcement_enabled" value="0">
                <input type="checkbox" name="announcement_enabled" value="1"
                       {{ old('announcement_enabled', $settings['announcement_enabled']) ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                Hiển thị thanh thông báo
            </label>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-5">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Hotline &amp; cam kết</h2>

            <div>
                <label for="site_hotline" class="form-label">Số hotline</label>
                <input id="site_hotline" name="site_hotline" type="text" maxlength="20"
                       value="{{ old('site_hotline', $settings['hotline']) }}"
                       placeholder="1900 0129"
                       class="form-input">
                <p class="text-[11px] text-slate-400 mt-1.5">Chỉ gồm số và các ký tự . + ( ) -. Link gọi điện được tạo tự động.</p>
            </div>

            <div>
                <label for="site_commitment" class="form-label">Câu cam kết (hiện cạnh hotline)</label>
                <input id="site_commitment" name="site_commitment" type="text" maxlength="40"
                       value="{{ old('site_commitment', $settings['commitment']) }}"
                       placeholder="Chính hãng 100%"
                       class="form-input">
                <p class="text-[11px] text-slate-400 mt-1.5">Để trống nếu không muốn hiện câu này.</p>
            </div>
        </div>

        <!-- Preview -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
            <p class="form-label">Xem trước</p>
            <div class="rounded-xl overflow-hidden border border-slate-200">
                <div class="bg-slate-900 text-slate-300 text-xs py-2 px-4 flex items-center justify-between">
                    <span class="flex items-center gap-2">
                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-400"></span>
                        <span class="font-medium text-slate-200">{{ $settings['announcement_text'] }}</span>
                    </span>
                    <span class="hidden sm:flex items-center gap-3">
                        <span>Hotline: {{ $settings['hotline'] }}</span>
                        <span>•</span>
                        <span>{{ $settings['commitment'] }}</span>
                    </span>
                </div>
            </div>
            <p class="text-[11px] text-slate-400 mt-2">
                Ẩn thanh thông báo sẽ làm header gọn hơn (nội dung cửa hàng dịch lên trên).
            </p>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="btn-primary">Lưu cài đặt</button>
            <a href="{{ route('admin.dashboard') }}" class="btn-secondary">Huỷ</a>
        </div>
    </form>
</div>
@endsection
