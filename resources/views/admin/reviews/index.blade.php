@extends('layouts.admin')

@section('title', 'Quản lý Đánh giá')
@section('page_title', 'Danh Sách Đánh Giá')

@section('content')
<div class="space-y-6">

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.reviews.index') }}" class="flex items-center gap-3 m-0">
            <select name="status" onchange="this.form.submit()" class="form-select text-xs font-semibold py-2 w-auto">
                <option value="">Tất cả trạng thái</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                <option value="hidden" {{ request('status') === 'hidden' ? 'selected' : '' }}>Đang ẩn</option>
            </select>
        </form>

        <div class="text-xs text-slate-500">
            Tổng cộng: <strong class="text-slate-800">{{ $reviews->total() }}</strong> đánh giá
        </div>
    </div>

    <!-- Reviews Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        @if($reviews->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50/80 text-xs text-slate-500 uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="p-4">Sản phẩm</th>
                            <th class="p-4">Khách hàng</th>
                            <th class="p-4">Điểm</th>
                            <th class="p-4">Nội dung</th>
                            <th class="p-4">Ngày</th>
                            <th class="p-4 text-center">Trạng thái</th>
                            <th class="p-4 text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($reviews as $review)
                            <tr class="hover:bg-slate-50/50 transition align-top">
                                <td class="p-4">
                                    @if($review->sanPham)
                                        <a href="{{ route('product.show', $review->sanPham->slug) }}" target="_blank" class="font-semibold text-slate-800 hover:text-rose-600 transition text-xs leading-snug block max-w-[220px]">
                                            {{ $review->sanPham->name }}
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-400">Sản phẩm đã xoá</span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <p class="text-xs font-bold text-slate-800">{{ $review->user->name ?? 'Khách hàng' }}</p>
                                    <p class="text-[11px] text-slate-400">{{ $review->user->email ?? '—' }}</p>
                                </td>
                                <td class="p-4">
                                    @include('partials.stars', ['rating' => $review->stars, 'size' => 'w-3.5 h-3.5'])
                                </td>
                                <td class="p-4 max-w-[320px]">
                                    @if($review->title)
                                        <p class="text-xs font-bold text-slate-800">{{ $review->title }}</p>
                                    @endif
                                    <p class="text-xs text-slate-600 leading-relaxed line-clamp-3">{{ $review->content ?: '—' }}</p>
                                </td>
                                <td class="p-4 text-xs text-slate-500 whitespace-nowrap">
                                    {{ $review->created_at?->format('d/m/Y H:i') }}
                                </td>
                                <td class="p-4 text-center">
                                    @if($review->is_approved)
                                        <span class="badge-success text-xs font-bold px-2.5 py-0.5">Đã duyệt</span>
                                    @else
                                        <span class="badge-warning text-xs font-bold px-2.5 py-0.5">Đang ẩn</span>
                                    @endif
                                </td>
                                <td class="p-4 text-center">
                                    <div class="inline-flex items-center gap-2">
                                        <form method="POST" action="{{ route('admin.reviews.toggle', $review->id) }}" class="m-0">
                                            @csrf
                                            <button type="submit" class="btn-sm {{ $review->is_approved ? 'btn-secondary' : 'btn-primary' }}">
                                                {{ $review->is_approved ? 'Ẩn' : 'Duyệt' }}
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.reviews.destroy', $review->id) }}" class="m-0" onsubmit="return confirm('Xoá vĩnh viễn đánh giá này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-sm btn-danger">Xoá</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-5 border-t border-slate-100">
                {{ $reviews->links() }}
            </div>
        @else
            <div class="p-12 text-center text-slate-400 text-sm">
                Chưa có đánh giá nào.
            </div>
        @endif
    </div>

</div>
@endsection
