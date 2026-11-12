@extends('layouts.admin')

@section('title', 'Quản lý Sản phẩm')
@section('page_title', 'Danh Sách Sản Phẩm')

@section('content')
<div class="space-y-6">

    <!-- Category Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.products.index') }}" class="flex items-center gap-3 m-0">
            <label for="category_id" class="text-xs font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">Danh mục:</label>
            <select name="category_id" id="category_id" onchange="this.form.submit()" class="form-select text-xs font-semibold py-2">
                <option value="">Tất cả danh mục</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </form>

        <div class="flex items-center gap-4">
            <div class="text-xs text-slate-500">
                Tổng cộng: <strong class="text-slate-800">{{ $products->total() }}</strong> sản phẩm
            </div>
            <a href="{{ route('admin.products.create') }}" class="btn-primary text-xs px-4 py-2">
                + Thêm sản phẩm
            </a>
        </div>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        @if($products->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50/80 text-xs text-slate-500 uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="p-4 sm:px-6 w-16">Ảnh</th>
                            <th class="p-4">Tên sản phẩm</th>
                            <th class="p-4">Danh mục</th>
                            <th class="p-4">Giá bán</th>
                            <th class="p-4">Biến thể & Tồn kho</th>
                            <th class="p-4 text-center">Trạng thái</th>
                            <th class="p-4 text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($products as $p)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="p-4 sm:px-6">
                                    <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center overflow-hidden p-1">
                                        <img src="{{ $p->image_url }}" alt="" class="max-h-full max-w-full object-contain">
                                    </div>
                                </td>
                                <td class="p-4">
                                    <a href="{{ route('product.show', $p->slug) }}" target="_blank" class="font-bold text-slate-900 hover:text-rose-600 transition flex items-center gap-1">
                                        <span>{{ $p->name }}</span>
                                        <span class="text-xs text-slate-400">↗</span>
                                    </a>
                                    <span class="text-xs text-slate-400 block mt-0.5">Slug: {{ $p->slug }}</span>
                                </td>
                                <td class="p-4 text-slate-600 font-medium">
                                    {{ $p->danhMuc?->name ?? '---' }}
                                </td>
                                <td class="p-4 font-black text-rose-600 whitespace-nowrap">
                                    {{ number_format($p->price, 0, '.', '.') }}đ
                                </td>
                                <td class="p-4 text-xs space-y-1">
                                    @foreach($p->bienThes as $v)
                                        <div>
                                            <code class="px-1.5 py-0.5 rounded bg-slate-100 font-semibold">{{ $v->sku }}</code>: 
                                            <strong class="text-slate-800">{{ $v->stock_qty }}</strong>
                                        </div>
                                    @endforeach
                                </td>
                                <td class="p-4 text-center">
                                    @if($p->is_active)
                                        <span class="badge-success text-xs font-bold px-2.5 py-0.5">
                                            Đang bán
                                        </span>
                                    @else
                                        <span class="badge-danger text-xs font-bold px-2.5 py-0.5">
                                            Đang ẩn
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 text-center">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('admin.products.edit', $p->id) }}" class="btn-sm btn-secondary">
                                            Sửa
                                        </a>
                                        <form method="POST" action="{{ route('admin.products.toggleStatus', $p->id) }}" class="m-0">
                                            @csrf
                                            <button type="submit" class="btn-sm {{ $p->is_active ? 'btn-danger' : 'btn-primary' }}">
                                                {{ $p->is_active ? 'Ẩn' : 'Hiện' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-5 border-t border-slate-100">
                {{ $products->links() }}
            </div>
        @else
            <div class="p-12 text-center text-slate-400 text-sm">
                Không có sản phẩm nào trong danh mục này.
            </div>
        @endif
    </div>

</div>
@endsection
