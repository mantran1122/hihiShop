@extends('layouts.admin')

@php
    $isEdit = $category !== null;
@endphp

@section('title', $isEdit ? 'Sửa danh mục' : 'Thêm danh mục')
@section('page_title', $isEdit ? 'Sửa Danh Mục' : 'Thêm Danh Mục Mới')

@section('content')
<div class="max-w-3xl">

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
          action="{{ $isEdit ? route('admin.categories.update', $category->id) : route('admin.categories.store') }}"
          class="space-y-6">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="form-label">Tên danh mục <span class="text-rose-600">*</span></label>
                    <input id="name" name="name" type="text" required maxlength="120"
                           value="{{ old('name', $category->name ?? '') }}"
                           placeholder="Ví dụ: Nước hoa" class="form-input">
                </div>

                <div>
                    <label for="slug" class="form-label">Slug</label>
                    <input id="slug" name="slug" type="text" maxlength="140"
                           value="{{ old('slug', $category->slug ?? '') }}"
                           placeholder="Để trống sẽ tự sinh từ tên" class="form-input font-mono text-xs">
                </div>
            </div>

            <div>
                <label for="description" class="form-label">Mô tả</label>
                <textarea id="description" name="description" rows="3" maxlength="500"
                          placeholder="Mô tả ngắn hiển thị ở trang danh mục" class="form-input resize-y">{{ old('description', $category->description ?? '') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label for="parent_id" class="form-label">Danh mục cha</label>
                    <select id="parent_id" name="parent_id" class="form-select">
                        <option value="">— Không có (danh mục gốc) —</option>
                        @foreach($parents as $parent)
                            <option value="{{ $parent->id }}"
                                {{ (string) old('parent_id', $category->parent_id ?? '') === (string) $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="position" class="form-label">Thứ tự hiển thị</label>
                    <input id="position" name="position" type="number" min="0" max="999"
                           value="{{ old('position', $category->position ?? 0) }}" class="form-input">
                </div>
            </div>

            <label class="flex items-center gap-2.5 text-sm font-semibold text-slate-700 cursor-pointer">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1"
                       {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                Hiển thị trên cửa hàng
            </label>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="btn-primary">{{ $isEdit ? 'Lưu thay đổi' : 'Tạo danh mục' }}</button>
            <a href="{{ route('admin.categories.index') }}" class="btn-secondary">Huỷ</a>
        </div>
    </form>
</div>
@endsection
