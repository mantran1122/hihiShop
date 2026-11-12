@extends('layouts.admin')

@php
    $isEdit = $brand !== null;
@endphp

@section('title', $isEdit ? 'Sửa thương hiệu' : 'Thêm thương hiệu')
@section('page_title', $isEdit ? 'Sửa Thương Hiệu' : 'Thêm Thương Hiệu Mới')

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
          action="{{ $isEdit ? route('admin.brands.update', $brand->id) : route('admin.brands.store') }}"
          class="space-y-6">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="form-label">Tên thương hiệu <span class="text-rose-600">*</span></label>
                    <input id="name" name="name" type="text" required maxlength="120"
                           value="{{ old('name', $brand->name ?? '') }}"
                           placeholder="Ví dụ: Burberry" class="form-input">
                </div>

                <div>
                    <label for="slug" class="form-label">Slug</label>
                    <input id="slug" name="slug" type="text" maxlength="140"
                           value="{{ old('slug', $brand->slug ?? '') }}"
                           placeholder="Để trống sẽ tự sinh" class="form-input font-mono text-xs">
                </div>
            </div>

            <div>
                <label for="origin" class="form-label">Xuất xứ</label>
                <input id="origin" name="origin" type="text" maxlength="80"
                       value="{{ old('origin', $brand->origin ?? '') }}"
                       placeholder="Ví dụ: Anh, Pháp, Ý" class="form-input">
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="btn-primary">{{ $isEdit ? 'Lưu thay đổi' : 'Tạo thương hiệu' }}</button>
            <a href="{{ route('admin.brands.index') }}" class="btn-secondary">Huỷ</a>
        </div>
    </form>
</div>
@endsection
