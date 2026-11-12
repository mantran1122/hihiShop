@extends('layouts.admin')

@php
    $isEdit = $product !== null;
@endphp

@section('title', $isEdit ? 'Sửa sản phẩm' : 'Thêm sản phẩm')
@section('page_title', $isEdit ? 'Sửa Sản Phẩm' : 'Thêm Sản Phẩm Mới')

@section('content')
<div class="max-w-5xl">

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
          action="{{ $isEdit ? route('admin.products.update', $product->id) : route('admin.products.store') }}"
          class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <!-- Cột chính -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-5">
                <div>
                    <label for="name" class="form-label">Tên sản phẩm <span class="text-rose-600">*</span></label>
                    <input id="name" name="name" type="text" required maxlength="255"
                           value="{{ old('name', $product->name ?? '') }}"
                           placeholder="Ví dụ: Nước hoa Citrus Fresh Eau De Parfum"
                           class="form-input @error('name') border-rose-400 @enderror">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="category_id" class="form-label">Danh mục <span class="text-rose-600">*</span></label>
                        <select id="category_id" name="category_id" required class="form-select">
                            <option value="">— Chọn danh mục —</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ (string) old('category_id', $product->category_id ?? '') === (string) $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="sku" class="form-label">SKU (mã phiên bản)</label>
                        <input id="sku" name="sku" type="text" maxlength="50"
                               value="{{ old('sku', $variant->sku ?? '') }}"
                               placeholder="Để trống sẽ tự sinh"
                               class="form-input font-mono uppercase">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="price" class="form-label">Giá bán (đ) <span class="text-rose-600">*</span></label>
                        <input id="price" name="price" type="number" required min="0" step="1000"
                               value="{{ old('price', isset($variant) && $variant ? (int) $variant->price : '') }}"
                               placeholder="990000" class="form-input">
                    </div>

                    <div>
                        <label for="stock_qty" class="form-label">Tồn kho <span class="text-rose-600">*</span></label>
                        <input id="stock_qty" name="stock_qty" type="number" required min="0" step="1"
                               value="{{ old('stock_qty', $variant->stock_qty ?? 0) }}"
                               placeholder="20" class="form-input">
                    </div>
                </div>

                <div>
                    <label for="short_desc" class="form-label">Mô tả ngắn</label>
                    <textarea id="short_desc" name="short_desc" rows="2" maxlength="500"
                              placeholder="Hiển thị dưới tên sản phẩm" class="form-input resize-y">{{ old('short_desc', $product->short_desc ?? '') }}</textarea>
                </div>

                <div>
                    <label for="description" class="form-label">Mô tả chi tiết</label>
                    <textarea id="description" name="description" rows="6" maxlength="5000"
                              placeholder="Thông tin chất liệu, bảo hành, hướng dẫn sử dụng..." class="form-input resize-y">{{ old('description', $product->description ?? '') }}</textarea>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
                <label for="image" class="form-label">Đường dẫn ảnh (trong thư mục public)</label>
                <input id="image" name="image" type="text" maxlength="255"
                       value="{{ old('image', $product?->anh?->firstWhere('is_cover', true)?->path ?? $product?->anh?->first()?->path ?? '') }}"
                       placeholder="images/nuocHoa/ten-thu-muc/anh.jpg"
                       class="form-input font-mono text-xs">
                <p class="text-[11px] text-slate-400 mt-2 leading-relaxed">
                    Serverless (Vercel) không ghi được file, nên ảnh phải có sẵn trong <code>public/images/...</code>.
                    Để trống nếu chưa có ảnh — hệ thống dùng ảnh minh hoạ.
                </p>
            </div>
        </div>

        <!-- Cột phụ -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
                <p class="form-label">Trạng thái</p>
                <label class="flex items-center gap-2.5 text-sm font-semibold text-slate-700 cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                    Hiển thị trên cửa hàng
                </label>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
                <p class="form-label">Ảnh hiện tại</p>
                <div class="aspect-square rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center p-3 overflow-hidden">
                    <img src="{{ $product?->image_url ?? asset('images/placeholder.svg') }}" alt="" class="max-h-full max-w-full object-contain">
                </div>
                @if($isEdit)
                    <p class="text-[11px] text-slate-400 mt-2 break-all">Slug: {{ $product->slug }}</p>
                @endif
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-3">
                <button type="submit" class="btn-primary w-full">
                    {{ $isEdit ? 'Lưu thay đổi' : 'Tạo sản phẩm' }}
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn-secondary w-full">
                    Huỷ
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
