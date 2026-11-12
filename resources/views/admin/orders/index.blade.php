@extends('layouts.admin')

@section('title', 'Quản lý Đơn hàng')
@section('page_title', 'Danh Sách Đơn Hàng')

@section('content')
<div class="space-y-6">

    <!-- Filter Pills -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm flex items-center gap-2 overflow-x-auto no-scrollbar">
        @php
            $currStatus = request('status');
            $filters = [
                '' => 'Tất cả đơn',
                'pending' => 'Chờ xử lý',
                'processing' => 'Đang xử lý',
                'shipped' => 'Đang giao',
                'completed' => 'Đã hoàn thành',
                'cancelled' => 'Đã hủy',
            ];
        @endphp
        @foreach($filters as $val => $label)
            <a href="{{ route('admin.orders.index', $val ? ['status' => $val] : []) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all {{ $currStatus === $val || ($val === '' && !$currStatus) ? 'bg-rose-600 text-white shadow-sm shadow-rose-600/30' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        @if($orders->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50/80 text-xs text-slate-500 uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="p-4 sm:px-6">Mã đơn</th>
                            <th class="p-4">Khách hàng</th>
                            <th class="p-4">Ngày tạo</th>
                            <th class="p-4">Số lượng</th>
                            <th class="p-4">Tổng tiền</th>
                            <th class="p-4">Trạng thái</th>
                            <th class="p-4 text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($orders as $o)
                            @php
                                $statusMap = [
                                    'pending' => ['Chờ xử lý', 'badge-warning'],
                                    'processing' => ['Đang xử lý', 'badge-info'],
                                    'shipped' => ['Đang giao', 'badge-purple'],
                                    'completed' => ['Hoàn thành', 'badge-success'],
                                    'delivered' => ['Đã giao', 'badge-success'],
                                    'cancelled' => ['Đã hủy', 'badge-danger'],
                                ];
                                $st = $statusMap[$o->status] ?? [$o->status, 'badge-info'];
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="p-4 sm:px-6 font-bold text-rose-600">
                                    <a href="{{ route('admin.orders.show', $o->id) }}" class="hover:underline">
                                        #{{ $o->code }}
                                    </a>
                                </td>
                                <td class="p-4">
                                    <strong class="text-slate-900 block">{{ $o->diaChi?->name ?? ($o->user?->name ?? 'Khách vãng lai') }}</strong>
                                    <span class="text-xs text-slate-400 font-medium">{{ $o->diaChi?->phone ?? '---' }}</span>
                                </td>
                                <td class="p-4 text-slate-500 text-xs">
                                    {{ $o->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="p-4 text-slate-700">
                                    {{ $o->mucs->sum('qty') }} món
                                </td>
                                <td class="p-4 font-black text-slate-900 whitespace-nowrap">
                                    {{ number_format($o->grand_total, 0, '.', '.') }}đ
                                </td>
                                <td class="p-4">
                                    <span class="{{ $st[1] }} px-2.5 py-1">
                                        {{ $st[0] }}
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <a href="{{ route('admin.orders.show', $o->id) }}" class="btn-primary btn-sm">
                                        Quản lý đơn
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-5 border-t border-slate-100">
                {{ $orders->links() }}
            </div>
        @else
            <div class="p-12 text-center text-slate-400 text-sm">
                Không tìm thấy đơn hàng nào phù hợp với bộ lọc hiện tại.
            </div>
        @endif
    </div>

</div>
@endsection
