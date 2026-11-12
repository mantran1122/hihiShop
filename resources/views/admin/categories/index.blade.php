@extends('layouts.admin')

@section('title', 'Quản lý Danh mục')
@section('page_title', 'Danh Sách Danh Mục')

@section('content')
<div class="space-y-6">

    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex items-center justify-between gap-4">
        <div class="text-xs text-slate-500">
            Tổng cộng: <strong class="text-slate-800">{{ $categories->count() }}</strong> danh mục
        </div>
        <a href="{{ route('admin.categories.create') }}" class="btn-primary text-xs px-4 py-2">+ Thêm danh mục</a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        @if($categories->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50/80 text-xs text-slate-500 uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="p-4">Tên danh mục</th>
                            <th class="p-4">Slug</th>
                            <th class="p-4">Danh mục cha</th>
                            <th class="p-4 text-center">Sản phẩm</th>
                            <th class="p-4 text-center">Vị trí</th>
                            <th class="p-4 text-center">Trạng thái</th>
                            <th class="p-4 text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($categories as $category)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="p-4 font-bold text-slate-800">{{ $category->name }}</td>
                                <td class="p-4">
                                    <code class="text-[11px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-600">{{ $category->slug }}</code>
                                </td>
                                <td class="p-4 text-xs text-slate-600">{{ $category->cha?->name ?? '—' }}</td>
                                <td class="p-4 text-center text-xs font-semibold text-slate-700">{{ (int) ($category->san_phams_count ?? 0) }}</td>
                                <td class="p-4 text-center text-xs text-slate-500">{{ $category->position }}</td>
                                <td class="p-4 text-center">
                                    @if($category->is_active)
                                        <span class="badge-success text-xs font-bold px-2.5 py-0.5">Đang bật</span>
                                    @else
                                        <span class="badge-danger text-xs font-bold px-2.5 py-0.5">Đang ẩn</span>
                                    @endif
                                </td>
                                <td class="p-4 text-center">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn-sm btn-secondary">Sửa</a>
                                        <form method="POST" action="{{ route('admin.categories.toggle', $category->id) }}" class="m-0">
                                            @csrf
                                            <button type="submit" class="btn-sm {{ $category->is_active ? 'btn-danger' : 'btn-primary' }}">
                                                {{ $category->is_active ? 'Ẩn' : 'Bật' }}
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
            <div class="p-12 text-center text-slate-400 text-sm">Chưa có danh mục nào.</div>
        @endif
    </div>

</div>
@endsection
