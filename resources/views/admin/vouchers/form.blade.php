@extends('layouts.admin')

@php
    $isEdit = $voucher !== null;
@endphp

@section('title', $isEdit ? 'Sửa voucher' : 'Thêm voucher')
@section('page_title', $isEdit ? 'Sửa Voucher' : 'Thêm Voucher Mới')

@section('content')
<div class="max-w-4xl">

    @if($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 shadow-sm">
            <p class="text-sm font-bold mb-1.5">Vui lòng kiểm tra lại:</p>
            <ul class="list-disc list-inside text-sm space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ $isEdit ? route('admin.vouchers.update', $voucher->id) : route('admin.vouchers.store') }}"
          class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="code" class="form-label">Mã voucher <span class="text-rose-600">*</span></label>
                        <input id="code" name="code" type="text" required maxlength="50"
                               value="{{ old('code', $voucher->code ?? '') }}"
                               placeholder="HIHI10" class="form-input font-mono uppercase">
                        <p class="text-[11px] text-slate-400 mt-1.5">Khách nhập mã này ở bước thanh toán.</p>
                    </div>

                    <div>
                        <label for="title" class="form-label">Tên hiển thị <span class="text-rose-600">*</span></label>
                        <input id="title" name="title" type="text" required maxlength="120"
                               value="{{ old('title', $voucher->title ?? '') }}"
                               placeholder="Giảm 10% cho đơn từ 250K" class="form-input">
                    </div>
                </div>

                <div>
                    <label for="description" class="form-label">Mô tả</label>
                    <textarea id="description" name="description" rows="2" maxlength="500"
                              placeholder="Hiển thị trong Kho Voucher ở trang thanh toán"
                              class="form-input resize-y">{{ old('description', $voucher->description ?? '') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="category" class="form-label">Loại voucher <span class="text-rose-600">*</span></label>
                        <select id="category" name="category" required class="form-select">
                            @php $categoryValue = old('category', $voucher->category ?? 'order_discount'); @endphp
                            <option value="order_discount" {{ $categoryValue === 'order_discount' ? 'selected' : '' }}>Giảm tiền hàng</option>
                            <option value="free_shipping" {{ $categoryValue === 'free_shipping' ? 'selected' : '' }}>Miễn phí vận chuyển</option>
                        </select>
                    </div>

                    <div>
                        <label for="type" class="form-label">Kiểu giảm <span class="text-rose-600">*</span></label>
                        <select id="type" name="type" required class="form-select">
                            @php $typeValue = old('type', $voucher->type ?? 'fixed'); @endphp
                            <option value="fixed" {{ $typeValue === 'fixed' ? 'selected' : '' }}>Số tiền cố định (đ)</option>
                            <option value="percent" {{ $typeValue === 'percent' ? 'selected' : '' }}>Phần trăm (%)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="value" class="form-label">Giá trị giảm <span class="text-rose-600">*</span></label>
                        <input id="value" name="value" type="number" required min="0" step="1000"
                               value="{{ old('value', $voucher ? (float) $voucher->value : '') }}"
                               placeholder="30000" class="form-input">
                    </div>

                    <div>
                        <label for="min_order" class="form-label">Đơn tối thiểu (đ)</label>
                        <input id="min_order" name="min_order" type="number" min="0" step="1000"
                               value="{{ old('min_order', $voucher ? (float) $voucher->min_order : 0) }}"
                               placeholder="0" class="form-input">
                    </div>

                    <div>
                        <label for="max_discount" class="form-label">Giảm tối đa (đ)</label>
                        <input id="max_discount" name="max_discount" type="number" min="0" step="1000"
                               value="{{ old('max_discount', $voucher && $voucher->max_discount !== null ? (float) $voucher->max_discount : '') }}"
                               placeholder="Để trống = không giới hạn" class="form-input">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-5">
                <p class="text-sm font-bold text-slate-900">Thời gian & giới hạn</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="starts_at" class="form-label">Bắt đầu</label>
                        <input id="starts_at" name="starts_at" type="datetime-local"
                               value="{{ old('starts_at', $voucher?->starts_at?->format('Y-m-d\TH:i')) }}"
                               class="form-input">
                    </div>

                    <div>
                        <label for="ends_at" class="form-label">Kết thúc</label>
                        <input id="ends_at" name="ends_at" type="datetime-local"
                               value="{{ old('ends_at', $voucher?->ends_at?->format('Y-m-d\TH:i')) }}"
                               class="form-input">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="usage_limit" class="form-label">Tổng lượt dùng tối đa</label>
                        <input id="usage_limit" name="usage_limit" type="number" min="1" step="1"
                               value="{{ old('usage_limit', $voucher->usage_limit ?? '') }}"
                               placeholder="Để trống = không giới hạn" class="form-input">
                    </div>

                    <div>
                        <label for="per_user_limit" class="form-label">Lượt dùng mỗi khách</label>
                        <input id="per_user_limit" name="per_user_limit" type="number" min="1" step="1"
                               value="{{ old('per_user_limit', $voucher->per_user_limit ?? '') }}"
                               placeholder="Ví dụ: 1" class="form-input">
                        <p class="text-[11px] text-slate-400 mt-1.5">Để trống = mỗi khách dùng không giới hạn.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
                <p class="form-label">Trạng thái</p>
                <label class="flex items-center gap-2.5 text-sm font-semibold text-slate-700 cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $voucher->is_active ?? true) ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                    Cho phép sử dụng
                </label>
            </div>

            @if($isEdit)
                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-2">
                    <p class="form-label">Thống kê</p>
                    <p class="text-xs text-slate-600">Đã dùng: <strong class="text-slate-900">{{ $voucher->used }}</strong> lượt</p>
                    <p class="text-xs text-slate-600">Giới hạn: <strong class="text-slate-900">{{ $voucher->usage_limit ?? 'Không giới hạn' }}</strong></p>
                </div>
            @endif

            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-3">
                <button type="submit" class="btn-primary w-full">
                    {{ $isEdit ? 'Lưu thay đổi' : 'Tạo voucher' }}
                </button>
                <a href="{{ route('admin.vouchers.index') }}" class="btn-secondary w-full">Huỷ</a>
            </div>
        </div>
    </form>
</div>
@endsection
