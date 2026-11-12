@extends('layouts.admin')

@section('title', 'Tổng quan - Dashboard')
@section('page_title', 'Bảng Điều Khiển Tổng Quan')

@section('content')
<div class="space-y-8">

    <!-- 4 KPI Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Stat 1: Revenue -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex flex-col justify-between hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Tổng doanh thu</span>
                <span class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                    💰
                </span>
            </div>
            <div class="mt-4">
                <strong class="text-2xl font-black text-rose-600 block">
                    {{ number_format($totalRevenue, 0, '.', '.') }}đ
                </strong>
                <span class="text-xs text-emerald-600 font-semibold mt-1 inline-flex items-center gap-1">
                    ✓ Các đơn không bị hủy
                </span>
            </div>
        </div>

        <!-- Stat 2: Total Orders -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex flex-col justify-between hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Tổng đơn hàng</span>
                <span class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold">
                    📦
                </span>
            </div>
            <div class="mt-4">
                <strong class="text-2xl font-black text-slate-900 block">
                    {{ $totalOrders }}
                </strong>
                <a href="{{ route('admin.orders.index') }}" class="text-xs text-sky-600 hover:text-sky-700 font-semibold mt-1 inline-block">
                    Xem danh sách đơn →
                </a>
            </div>
        </div>

        <!-- Stat 3: Pending Orders -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex flex-col justify-between hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Đơn chờ xử lý</span>
                <span class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                    ⏳
                </span>
            </div>
            <div class="mt-4">
                <strong class="text-2xl font-black text-amber-600 block">
                    {{ $pendingOrders }}
                </strong>
                <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="text-xs text-amber-600 hover:text-amber-700 font-semibold mt-1 inline-block">
                    Xử lý đơn mới ngay →
                </a>
            </div>
        </div>

        <!-- Stat 4: Users -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex flex-col justify-between hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Tài khoản khách</span>
                <span class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                    👥
                </span>
            </div>
            <div class="mt-4">
                <strong class="text-2xl font-black text-slate-900 block">
                    {{ $totalUsers }}
                </strong>
                <a href="{{ route('admin.users') }}" class="text-xs text-purple-600 hover:text-purple-700 font-semibold mt-1 inline-block">
                    Phân quyền tài khoản →
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-5 sm:px-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">Đơn hàng mới nhất</h2>
                <p class="text-xs text-slate-500 mt-0.5">Các đơn vừa được khách đặt qua hệ thống</p>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-rose-600 hover:text-rose-700 flex items-center gap-1">
                <span>Tất cả đơn</span>
                <span>→</span>
            </a>
        </div>

        @if($recentOrders->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50/80 text-xs text-slate-500 uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="p-4 sm:px-6">Mã đơn</th>
                            <th class="p-4">Khách hàng</th>
                            <th class="p-4">Thời gian</th>
                            <th class="p-4">Tổng tiền</th>
                            <th class="p-4">Trạng thái</th>
                            <th class="p-4 text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($recentOrders as $o)
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
                                <td class="p-4 font-black text-slate-900">
                                    {{ number_format($o->grand_total, 0, '.', '.') }}đ
                                </td>
                                <td class="p-4">
                                    <span class="{{ $st[1] }} px-2.5 py-1">
                                        {{ $st[0] }}
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <a href="{{ route('admin.orders.show', $o->id) }}" class="btn-secondary btn-sm">
                                        Chi tiết
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-8 text-center text-slate-400 text-sm">
                Chưa có đơn hàng nào trong hệ thống.
            </div>
        @endif
    </div>

</div>
@endsection
