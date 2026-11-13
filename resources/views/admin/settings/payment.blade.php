@extends('layouts.admin')

@section('title', 'Cài đặt thanh toán')
@section('page_title', 'Cài Đặt Thanh Toán VietQR')

@section('content')
<div class="max-w-5xl space-y-6">

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 shadow-sm">
            <p class="text-sm font-bold mb-1.5">Vui lòng kiểm tra lại:</p>
            <ul class="list-disc list-inside text-sm space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="p-4 rounded-2xl bg-sky-50 border border-sky-200 text-sky-800 text-sm leading-relaxed">
        Thông tin này dùng để sinh mã VietQR ở trang thanh toán. Thay đổi có hiệu lực <strong>ngay lập tức</strong>,
        không cần deploy lại.
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <form method="POST" action="{{ route('admin.settings.payment.update') }}" class="lg:col-span-2 space-y-6">
            @csrf

            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="bank_id" class="form-label">Ngân hàng <span class="text-rose-600">*</span></label>
                        <select id="bank_id" name="bank_id" required class="form-select">
                            @foreach($banks as $code => $name)
                                <option value="{{ $code }}" {{ $settings['bank_id'] === $code ? 'selected' : '' }}>
                                    {{ $code }} — {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="bank_name" class="form-label">Tên ngân hàng hiển thị <span class="text-rose-600">*</span></label>
                        <input id="bank_name" name="bank_name" type="text" required maxlength="120"
                               value="{{ $settings['bank_name'] }}" class="form-input">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="account_no" class="form-label">Số tài khoản nhận tiền <span class="text-rose-600">*</span></label>
                        <input id="account_no" name="account_no" type="text" required maxlength="30"
                               value="{{ $settings['account_no'] }}"
                               placeholder="0988889999" class="form-input font-mono">
                        <p class="text-[11px] text-slate-400 mt-1.5">Kiểm tra kỹ — khách chuyển tiền vào số này.</p>
                    </div>

                    <div>
                        <label for="account_name" class="form-label">Tên chủ tài khoản <span class="text-rose-600">*</span></label>
                        <input id="account_name" name="account_name" type="text" required maxlength="120"
                               value="{{ $settings['account_name'] }}"
                               placeholder="HIHI SHOP" class="form-input">
                    </div>
                </div>

                <div>
                    <label for="template" class="form-label">Mẫu mã QR</label>
                    <select id="template" name="template" class="form-select">
                        @foreach(['compact2' => 'compact2 — gọn, có logo ngân hàng', 'compact' => 'compact — gọn', 'qr_only' => 'qr_only — chỉ mã QR', 'print' => 'print — khổ in'] as $value => $label)
                            <option value="{{ $value }}" {{ $settings['template'] === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="btn-primary">Lưu cài đặt</button>
                <a href="{{ route('admin.dashboard') }}" class="btn-secondary">Huỷ</a>
            </div>
        </form>

        <!-- Preview -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm h-fit">
            <p class="form-label">Xem trước mã QR</p>
            <div class="rounded-xl border border-slate-100 bg-slate-50 p-3 flex items-center justify-center">
                <img src="https://img.vietqr.io/image/{{ $settings['bank_id'] }}-{{ $settings['account_no'] }}-{{ $settings['template'] }}.png?amount=100000&addInfo={{ urlencode('HIHI DEMO') }}&accountName={{ urlencode($settings['account_name']) }}"
                     alt="Mã QR xem trước" class="max-w-full rounded-lg">
            </div>
            <div class="mt-3 text-xs text-slate-500 space-y-1">
                <p>Ngân hàng: <strong class="text-slate-800">{{ $settings['bank_id'] }}</strong></p>
                <p>Số TK: <strong class="text-slate-800 font-mono">{{ $settings['account_no'] }}</strong></p>
                <p>Chủ TK: <strong class="text-slate-800">{{ $settings['account_name'] }}</strong></p>
            </div>
            <p class="text-[11px] text-slate-400 mt-3 leading-relaxed">
                Ảnh xem trước dùng số tiền mẫu 100.000đ. Mã thật ở trang thanh toán sẽ tự điền đúng số tiền và mã đơn.
            </p>
        </div>
    </div>
</div>
@endsection
