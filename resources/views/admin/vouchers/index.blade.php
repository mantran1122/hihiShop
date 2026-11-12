@extends('layouts.admin')

@section('title', 'Quản lý Voucher')
@section('page_title', 'Danh Sách Voucher')

@section('content')
<div class="space-y-6">

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.vouchers.index') }}" class="flex flex-wrap items-center gap-3 m-0">
            <select name="category" onchange="this.form.submit()" class="form-select text-xs font-semibold py-2 w-auto">
                <option value="">Tất cả loại</option>
                <option value="order_discount" {{ request('category') === 'order_discount' ? 'selected' : '' }}>Giảm tiền hàng</option>
                <option value="free_shipping" {{ request('category') === 'free_shipping' ? 'selected' : '' }}>Miễn phí vận chuyển</option>
            </select>

            <select name="status" onchange="this.form.submit()" class="form-select text-xs font-semibold py-2 w-auto">
                <option value="">Tất cả trạng thái</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Đang bật</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tạm ngưng</option>
            </select>
        </form>

        <div class="flex items-center gap-4">
            <div class="text-xs text-slate-500">
                Tổng cộng: <strong class="text-slate-800">{{ $vouchers->total() }}</strong> voucher
            </div>
            <a href="{{ route('admin.vouchers.create') }}" class="btn-primary text-xs px-4 py-2">
                + Thêm voucher
            </a>
        </div>
    </div>

    <!-- Voucher Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        @if($vouchers->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50/80 text-xs text-slate-500 uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="p-4">Mã</th>
                            <th class="p-4">Mô tả</th>
                            <th class="p-4">Giá trị</th>
                            <th class="p-4">Điều kiện</th>
                            <th class="p-4 text-center">Lượt dùng</th>
                            <th class="p-4">Thời hạn</th>
                            <th class="p-4 text-center">Trạng thái</th>
                            <th class="p-4 text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($vouchers as $voucher)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="p-4">
                                    <span class="font-mono font-black text-slate-900 text-xs px-2 py-1 rounded-lg bg-slate-100">{{ $voucher->code }}</span>
                                </td>
                                <td class="p-4">
                                    <p class="font-bold text-slate-800 leading-snug">{{ $voucher->title ?? '—' }}</p>
                                    <span class="text-[11px] font-semibold {{ $voucher->category === 'free_shipping' ? 'text-sky-600' : 'text-rose-600' }}">
                                        {{ $voucher->category === 'free_shipping' ? 'Miễn phí vận chuyển' : 'Giảm tiền hàng' }}
                                    </span>
                                </td>
                                <td class="p-4 font-black whitespace-nowrap {{ $voucher->category === 'free_shipping' ? 'text-sky-600' : 'text-rose-600' }}">
                                    {{ $voucher->type === 'percent' ? rtrim(rtrim(number_format((float) $voucher->value, 2, '.', ''), '0'), '.').'%' : number_format((float) $voucher->value, 0, '.', '.').'đ' }}
                                    @if($voucher->max_discount)
                                        <span class="block text-[10px] font-semibold text-slate-400">tối đa {{ number_format((float) $voucher->max_discount, 0, '.', '.') }}đ</span>
                                    @endif
                                </td>
                                <td class="p-4 text-xs text-slate-600">
                                    Đơn từ <strong class="text-slate-800">{{ number_format((float) $voucher->min_order, 0, '.', '.') }}đ</strong>
                                </td>
                                <td class="p-4 text-center text-xs font-semibold text-slate-600">
                                    {{ $voucher->used }} / {{ $voucher->usage_limit ?? '∞' }}
                                </td>
                                <td class="p-4 text-xs text-slate-500 whitespace-nowrap">
                                    @if($voucher->starts_at || $voucher->ends_at)
                                        {{ $voucher->starts_at?->format('d/m/Y') ?? '...' }}
                                        →
                                        {{ $voucher->ends_at?->format('d/m/Y') ?? '...' }}
                                    @else
                                        Không giới hạn
                                    @endif
                                </td>
                                <td class="p-4 text-center">
                                    @if($voucher->is_active)
                                        <span class="badge-success text-xs font-bold px-2.5 py-0.5">Đang bật</span>
                                    @else
                                        <span class="badge-danger text-xs font-bold px-2.5 py-0.5">Tạm ngưng</span>
                                    @endif
                                </td>
                                <td class="p-4 text-center">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('admin.vouchers.edit', $voucher->id) }}" class="btn-sm btn-secondary">Sửa</a>
                                        <form method="POST" action="{{ route('admin.vouchers.toggle', $voucher->id) }}" class="m-0">
                                            @csrf
                                            <button type="submit" class="btn-sm {{ $voucher->is_active ? 'btn-danger' : 'btn-primary' }}">
                                                {{ $voucher->is_active ? 'Tắt' : 'Bật' }}
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
                {{ $vouchers->links() }}
            </div>
        @else
            <div class="p-12 text-center text-slate-400 text-sm">
                Chưa có voucher nào. Bấm "Thêm voucher" để tạo mã giảm giá đầu tiên.
            </div>
        @endif
    </div>

</div>
@endsection
