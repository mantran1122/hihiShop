@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng ' . $order->code)
@section('page_title', 'Chi Tiết Đơn Hàng #' . $order->code)

@section('content')
<div class="max-w-4xl space-y-6">

    <div>
        <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-rose-600 transition">
            <span>← Quay lại danh sách đơn hàng</span>
        </a>
    </div>

    <!-- Quick Status Update Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Trạng thái hiện tại</span>
            <div class="mt-1">
                @php
                    $statusMap = [
                        'pending' => ['Chờ xử lý', 'badge-warning'],
                        'processing' => ['Đang xử lý', 'badge-info'],
                        'shipped' => ['Đang giao hàng', 'badge-purple'],
                        'completed' => ['Hoàn thành', 'badge-success'],
                        'delivered' => ['Đã giao', 'badge-success'],
                        'cancelled' => ['Đã hủy', 'badge-danger'],
                    ];
                    $st = $statusMap[$order->status] ?? [$order->status, 'badge-info'];
                @endphp
                <span class="{{ $st[1] }} px-3 py-1 text-xs font-bold">
                    {{ $st[0] }}
                </span>
            </div>
        </div>

        <!-- Form status update -->
        <form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}" class="flex items-center gap-2 m-0">
            @csrf
            <select name="status" id="status" class="form-select text-xs font-semibold py-2">
                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Đang giao hàng</option>
                <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Hủy đơn hàng</option>
            </select>
            <button type="submit" class="btn-primary btn-sm py-2 px-4 whitespace-nowrap">
                Cập nhật
            </button>
        </form>
    </div>

    <!-- Customer & Payment Info Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider pb-3 border-b border-slate-100 mb-3">
                Thông tin người nhận
            </h3>
            @if($order->diaChi)
                <div class="text-sm text-slate-700 space-y-1.5">
                    <p><strong>Họ và tên:</strong> {{ $order->diaChi->name }}</p>
                    <p><strong>Điện thoại:</strong> {{ $order->diaChi->phone }}</p>
                    <p><strong>Tỉnh/Thành:</strong> {{ $order->diaChi->province ?: 'N/A' }}</p>
                    <p><strong>Địa chỉ:</strong> {{ $order->diaChi->address_line }}</p>
                    @if($order->shipping_service)
                        <p class="pt-1 text-xs text-rose-600 font-bold">
                            Vận chuyển: {{ $order->shipping_service }} ({{ $order->shipping_carrier }})
                        </p>
                    @endif
                </div>
            @else
                <p class="text-sm text-slate-500">Không có thông tin địa chỉ cụ thể.</p>
            @endif
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider pb-3 border-b border-slate-100 mb-3">
                Thông tin thanh toán
            </h3>
            <div class="text-sm text-slate-700 space-y-1.5">
                <p><strong>Ngày tạo:</strong> {{ $order->created_at->format('d/m/Y H:i:s') }}</p>
                <p><strong>Phương thức:</strong> {{ ($order->payment?->provider ?? 'cod') === 'vietqr' ? 'Quét mã VietQR 24/7' : 'Tiền mặt khi nhận hàng (COD)' }}</p>
                @if($order->payment?->transaction_id)
                    <p><strong>Mã giao dịch:</strong> <code class="font-mono text-xs bg-slate-100 px-1.5 py-0.5 rounded text-rose-600 font-semibold">{{ $order->payment->transaction_id }}</code></p>
                @endif
                <p>
                    <strong>Trạng thái TT:</strong> 
                    <span class="font-bold {{ $order->payment_status === 'paid' ? 'text-emerald-600' : 'text-amber-600' }}">
                        {{ $order->payment_status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                    </span>
                </p>
                <p><strong>Giao hàng:</strong> <span class="capitalize">{{ $order->shipping_status }}</span></p>
            </div>
        </div>
    </div>

    <!-- Order Items Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 sm:px-6 border-b border-slate-100">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Sản phẩm trong đơn</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 text-xs text-slate-500 uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="p-4 sm:px-6">Sản phẩm</th>
                        <th class="p-4">Mã SKU</th>
                        <th class="p-4">Đơn giá</th>
                        <th class="p-4 text-center">Số lượng</th>
                        <th class="p-4 text-right">Thành tiền</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($order->mucs as $it)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="p-4 sm:px-6 font-bold text-slate-900">
                                {{ $it->name_snapshot }}
                            </td>
                            <td class="p-4 text-slate-500">
                                <code class="px-1.5 py-0.5 rounded bg-slate-100 text-xs font-semibold">{{ $it->sku }}</code>
                            </td>
                            <td class="p-4 font-semibold text-slate-700">
                                {{ number_format($it->price, 0, '.', '.') }}đ
                            </td>
                            <td class="p-4 text-center font-bold text-slate-800">
                                {{ $it->qty }}
                            </td>
                            <td class="p-4 text-right font-black text-slate-900">
                                {{ number_format($it->subtotal, 0, '.', '.') }}đ
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-5 sm:px-6 bg-slate-50/80 border-t border-slate-100 flex flex-col items-end space-y-1.5 text-sm text-slate-600">
            <div>Tiền hàng: <strong class="text-slate-900">{{ number_format($order->subtotal, 0, '.', '.') }}đ</strong></div>
            @if($order->discount_total > 0)
                <div class="text-emerald-600">Giảm giá shop ({{ $order->coupon_code }}): <strong>-{{ number_format($order->discount_total, 0, '.', '.') }}đ</strong></div>
            @endif
            <div>Phí vận chuyển gốc: <strong class="text-slate-900">{{ number_format($order->shipping_total, 0, '.', '.') }}đ</strong></div>
            @if($order->shipping_discount > 0)
                <div class="text-emerald-600">Freeship trợ giá: <strong>-{{ number_format($order->shipping_discount, 0, '.', '.') }}đ</strong></div>
            @endif
            <div class="text-lg font-black text-rose-600 border-t border-slate-200 pt-2 mt-1">
                Tổng thanh toán: {{ number_format($order->grand_total, 0, '.', '.') }}đ
            </div>
        </div>
    </div>

</div>
@endsection
