@extends('layouts.shop')

@section('content')
<div class="py-4">
    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-2 text-xs sm:text-sm text-slate-500 mb-6" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-rose-600 transition flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Trang chủ
        </a>
        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-900 font-bold">Đơn hàng của tôi</span>
    </nav>

    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mb-6 flex items-center gap-2.5">
        <span>📦 Đơn hàng của tôi</span>
    </h1>

    @if($orders->count() > 0)
        <!-- Desktop Table View -->
        <div class="hidden sm:block bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden mb-6">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 text-xs text-slate-500 uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="p-4 sm:px-6">Mã đơn</th>
                        <th class="p-4">Ngày đặt</th>
                        <th class="p-4">Số lượng</th>
                        <th class="p-4">Tổng tiền</th>
                        <th class="p-4">Thanh toán</th>
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
                                'completed' => ['Đã giao thành công', 'badge-success'],
                                'delivered' => ['Đã nhận hàng', 'badge-success'],
                                'cancelled' => ['Đã hủy', 'badge-danger'],
                            ];
                            $st = $statusMap[$o->status] ?? [$o->status, 'badge-info'];
                            $isPaid = $o->payment_status === 'paid';
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="p-4 sm:px-6 font-bold text-rose-600">
                                <a href="{{ route('orders.show', $o->code) }}" class="hover:underline">
                                    {{ $o->code }}
                                </a>
                            </td>
                            <td class="p-4 text-slate-500 font-medium">
                                {{ $o->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="p-4 text-slate-700">
                                {{ $o->mucs->sum('qty') }} món
                            </td>
                            <td class="p-4 font-black text-slate-900 whitespace-nowrap">
                                {{ number_format($o->grand_total, 0, '.', '.') }}đ
                            </td>
                            <td class="p-4">
                                @if($isPaid)
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        Đã thanh toán
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200">
                                        Chưa thanh toán
                                    </span>
                                @endif
                            </td>
                            <td class="p-4">
                                <span class="{{ $st[1] }} px-2.5 py-1">
                                    {{ $st[0] }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <a href="{{ route('orders.show', $o->code) }}" class="btn-secondary btn-sm">
                                    Chi tiết →
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="sm:hidden space-y-3 mb-6">
            @foreach($orders as $o)
                @php
                    $statusMap = [
                        'pending' => ['Chờ xử lý', 'badge-warning'],
                        'processing' => ['Đang xử lý', 'badge-info'],
                        'shipped' => ['Đang giao', 'badge-purple'],
                        'completed' => ['Đã giao thành công', 'badge-success'],
                        'delivered' => ['Đã nhận hàng', 'badge-success'],
                        'cancelled' => ['Đã hủy', 'badge-danger'],
                    ];
                    $st = $statusMap[$o->status] ?? [$o->status, 'badge-info'];
                @endphp
                <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <a href="{{ route('orders.show', $o->code) }}" class="font-bold text-rose-600 text-sm">
                            #{{ $o->code }}
                        </a>
                        <span class="{{ $st[1] }} text-[11px] px-2 py-0.5">
                            {{ $st[0] }}
                        </span>
                    </div>

                    <div class="text-xs text-slate-500 space-y-1">
                        <div>Ngày đặt: <strong class="text-slate-700">{{ $o->created_at->format('d/m/Y H:i') }}</strong></div>
                        <div>Số món: <strong class="text-slate-700">{{ $o->mucs->sum('qty') }} món</strong></div>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                        <span class="text-base font-black text-slate-900">
                            {{ number_format($o->grand_total, 0, '.', '.') }}đ
                        </span>
                        <a href="{{ route('orders.show', $o->code) }}" class="btn-secondary btn-sm">
                            Xem chi tiết
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div>
            {{ $orders->links() }}
        </div>
    @else
        <div class="bg-white rounded-3xl border border-slate-200/80 p-12 text-center max-w-lg mx-auto shadow-sm">
            <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">Bạn chưa có đơn hàng nào</h3>
            <p class="text-sm text-slate-500 mb-6">Hãy đặt hàng ngay để trải nghiệm dịch vụ mua sắm & thanh toán tiện lợi của HIHI-SHOP!</p>
            <a href="{{ route('home') }}" class="btn-primary">
                Bắt đầu mua sắm ngay →
            </a>
        </div>
    @endif
</div>
@endsection
