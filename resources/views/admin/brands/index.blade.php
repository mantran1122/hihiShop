@extends('layouts.admin')

@section('title', 'Quản lý Thương hiệu')
@section('page_title', 'Danh Sách Thương Hiệu')

@section('content')
<div class="space-y-6">

    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex items-center justify-between gap-4">
        <div class="text-xs text-slate-500">
            Tổng cộng: <strong class="text-slate-800">{{ $brands->count() }}</strong> thương hiệu
        </div>
        <a href="{{ route('admin.brands.create') }}" class="btn-primary text-xs px-4 py-2">+ Thêm thương hiệu</a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        @if($brands->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50/80 text-xs text-slate-500 uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="p-4">Tên thương hiệu</th>
                            <th class="p-4">Slug</th>
                            <th class="p-4">Xuất xứ</th>
                            <th class="p-4 text-center">Sản phẩm</th>
                            <th class="p-4 text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($brands as $brand)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="p-4 font-bold text-slate-800">{{ $brand->name }}</td>
                                <td class="p-4">
                                    <code class="text-[11px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-600">{{ $brand->slug }}</code>
                                </td>
                                <td class="p-4 text-xs text-slate-600">{{ $brand->origin ?? '—' }}</td>
                                <td class="p-4 text-center text-xs font-semibold text-slate-700">{{ (int) ($brand->san_phams_count ?? 0) }}</td>
                                <td class="p-4 text-center">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('admin.brands.edit', $brand->id) }}" class="btn-sm btn-secondary">Sửa</a>
                                        <form method="POST" action="{{ route('admin.brands.destroy', $brand->id) }}" class="m-0" onsubmit="return confirm('Xoá thương hiệu này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-sm btn-danger" {{ (int) ($brand->san_phams_count ?? 0) > 0 ? 'disabled' : '' }}>
                                                Xoá
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-12 text-center text-slate-400 text-sm">Chưa có thương hiệu nào.</div>
        @endif
    </div>

</div>
@endsection
