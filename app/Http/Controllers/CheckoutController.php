<?php

namespace App\Http\Controllers;

use App\Models\GioHang;
use App\Models\BienTheSanPham;
use App\Models\DonHang;
use App\Models\MucDonHang;
use App\Models\DiaChi;
use App\Models\ThanhToan;
use App\Models\VanChuyen;
use App\Models\Coupon;
use App\Models\Setting;
use App\Services\ShippingService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function index(Request $req)
    {
        if (!Auth::check()) {
            session()->put('url.intended', route('checkout.index'));
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiến hành thanh toán.');
        }

        $cart = GioHang::for($req->user(), $req->session()->getId());
        $cart->load('mucs.sanPham','mucs.bienThe');
        abort_if($cart->mucs()->count() === 0, 302, '', ['Location'=>route('cart.index')]);

        $subtotal = (float) $cart->mucs()->sum('line_total');
        $itemCount = (int) $cart->mucs()->sum('qty');

        $provincesList = array_keys(ShippingService::getProvinces());
        $defaultProvince = 'Hà Nội';

        // Tính cước vận chuyển khởi tạo
        $shippingData = ShippingService::getRates($defaultProvince, $itemCount, $subtotal);

        // Lấy danh sách Voucher khả dụng của cửa hàng
        $shopVouchers = Coupon::where('is_active', true)
            ->where('category', 'order_discount')
            ->where(function($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->latest()
            ->get();

        $shippingVouchers = Coupon::where('is_active', true)
            ->where('category', 'free_shipping')
            ->where(function($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->latest()
            ->get();

        return view('checkout', compact(
            'cart',
            'provincesList',
            'defaultProvince',
            'shippingData',
            'shopVouchers',
            'shippingVouchers',
            'subtotal',
            'itemCount'
        ));
    }

    /**
     * API Tính toán phí ship và áp dụng Voucher động theo thời gian thực (Ajax)
     */
    public function calculate(Request $req)
    {
        $cart = GioHang::for($req->user(), $req->session()->getId());
        $subtotal = (float) $cart->mucs()->sum('line_total');
        $itemCount = (int) $cart->mucs()->sum('qty');

        $province = $req->input('province', 'Hà Nội');
        $serviceId = $req->input('shipping_service', 'fast');
        $couponCode = strtoupper(trim($req->input('coupon_code', '')));
        $shippingCouponCode = strtoupper(trim($req->input('shipping_coupon_code', '')));

        // 1. Tính toán bảng cước vận chuyển chuẩn Shopee
        $shippingData = ShippingService::getRates($province, $itemCount, $subtotal);
        $rates = $shippingData['methods'];
        $selectedService = $rates[$serviceId] ?? ($rates['fast'] ?? reset($rates));

        $baseShippingFee = (float) $selectedService['base_fee'];
        $xtraShippingDiscount = (float) $selectedService['discount']; // Freeship Xtra mặc định của hệ thống
        $shippingFeeAfterXtra = (float) $selectedService['final_fee'];

        // 2. Tính Shop Voucher (Giảm tiền hàng)
        $orderDiscount = 0;
        $orderCouponMessage = null;
        $orderCouponValid = false;
        $orderCouponTitle = null;

        if ($couponCode !== '') {
            $coupon = Coupon::where('code', $couponCode)->where('category', 'order_discount')->first();
            if ($coupon) {
                [$valid, $msg] = $coupon->isValidFor($subtotal, 0, $req->user());
                if ($valid) {
                    $orderDiscount = (float) $coupon->calculateDiscount($subtotal);
                    $orderCouponValid = true;
                    $orderCouponTitle = $coupon->title;
                    $orderCouponMessage = "Áp dụng thành công: Giảm " . number_format($orderDiscount, 0, '.', '.') . "đ vào tiền hàng.";
                } else {
                    $orderCouponMessage = $msg;
                }
            } else {
                $orderCouponMessage = "Mã voucher giảm giá không tồn tại.";
            }
        }

        // 3. Tính Freeship Voucher (Giảm phí vận chuyển)
        $voucherShippingDiscount = 0;
        $shippingCouponMessage = null;
        $shippingCouponValid = false;
        $shippingCouponTitle = null;

        if ($shippingCouponCode !== '') {
            $shipCoupon = Coupon::where('code', $shippingCouponCode)->where('category', 'free_shipping')->first();
            if ($shipCoupon) {
                [$valid, $msg] = $shipCoupon->isValidFor($subtotal, $shippingFeeAfterXtra, $req->user());
                if ($valid) {
                    $voucherShippingDiscount = (float) $shipCoupon->calculateDiscount($subtotal, $shippingFeeAfterXtra);
                    $shippingCouponValid = true;
                    $shippingCouponTitle = $shipCoupon->title;
                    $shippingCouponMessage = "Áp dụng thành công: Giảm thêm " . number_format($voucherShippingDiscount, 0, '.', '.') . "đ phí ship.";
                } else {
                    $shippingCouponMessage = $msg;
                }
            } else {
                $shippingCouponMessage = "Mã voucher miễn phí vận chuyển không tồn tại.";
            }
        }

        $totalShippingDiscount = $xtraShippingDiscount + $voucherShippingDiscount;
        $finalShippingFee = max(0, $baseShippingFee - $totalShippingDiscount);
        $grandTotal = max(0, $subtotal - $orderDiscount + $finalShippingFee);

        return response()->json([
            'success' => true,
            'province' => $province,
            'item_count' => $itemCount,
            'subtotal' => $subtotal,
            'shipping_rates' => $rates,
            'selected_service' => $selectedService,
            'base_shipping_fee' => $baseShippingFee,
            'xtra_discount' => $xtraShippingDiscount,
            'voucher_shipping_discount' => $voucherShippingDiscount,
            'total_shipping_discount' => $totalShippingDiscount,
            'final_shipping_fee' => $finalShippingFee,
            'order_discount' => $orderDiscount,
            'order_coupon_valid' => $orderCouponValid,
            'order_coupon_title' => $orderCouponTitle,
            'order_coupon_message' => $orderCouponMessage,
            'shipping_coupon_valid' => $shippingCouponValid,
            'shipping_coupon_title' => $shippingCouponTitle,
            'shipping_coupon_message' => $shippingCouponMessage,
            'grand_total' => $grandTotal,
        ]);
    }

    public function place(Request $req)
    {
        if (!Auth::check()) {
            session()->put('url.intended', route('checkout.index'));
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để đặt hàng.');
        }

        $req->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'province' => 'required|string|max:100',
            'address' => 'required|string|max:500',
            'shipping_service' => 'required|string|in:instant,fast,economy',
            'payment_method' => 'required|string|in:cod,vietqr',
            'coupon_code' => 'nullable|string|max:50',
            'shipping_coupon_code' => 'nullable|string|max:50',
        ]);

        $cart = GioHang::for($req->user(), $req->session()->getId());
        $cart->load('mucs.sanPham','mucs.bienThe');

        if ($cart->mucs()->count() === 0) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        // Kiểm tra tồn kho trước khi đặt
        foreach ($cart->mucs as $it) {
            if ($it->bienThe && $it->bienThe->stock_qty < $it->qty) {
                return redirect()->route('cart.index')->with('error', "Sản phẩm '{$it->sanPham->name} ({$it->bienThe->sku})' không đủ số lượng trong kho.");
            }
        }

        $subtotal = (float) $cart->mucs()->sum('line_total');
        $itemCount = (int) $cart->mucs()->sum('qty');
        $province = $req->province;
        $serviceId = $req->shipping_service;
        $couponCode = strtoupper(trim($req->input('coupon_code', '')));
        $shippingCouponCode = strtoupper(trim($req->input('shipping_coupon_code', '')));
        $paymentMethod = $req->input('payment_method', 'cod');

        // Tính toán cước phí và voucher trên server
        $shippingData = ShippingService::getRates($province, $itemCount, $subtotal);
        $rates = $shippingData['methods'];
        $selectedService = $rates[$serviceId] ?? ($rates['fast'] ?? reset($rates));

        $baseShippingFee = (float) $selectedService['base_fee'];
        $xtraShippingDiscount = (float) $selectedService['discount'];
        $shippingFeeAfterXtra = (float) $selectedService['final_fee'];

        // Shop Voucher
        $orderDiscount = 0;
        $validCoupon = null;
        if ($couponCode !== '') {
            $coupon = Coupon::where('code', $couponCode)->where('category', 'order_discount')->first();
            if ($coupon) {
                [$valid] = $coupon->isValidFor($subtotal, 0, $req->user());
                if ($valid) {
                    $orderDiscount = (float) $coupon->calculateDiscount($subtotal);
                    $validCoupon = $coupon;
                }
            }
        }

        // Freeship Voucher
        $voucherShippingDiscount = 0;
        $validShipCoupon = null;
        if ($shippingCouponCode !== '') {
            $shipCoupon = Coupon::where('code', $shippingCouponCode)->where('category', 'free_shipping')->first();
            if ($shipCoupon) {
                [$valid] = $shipCoupon->isValidFor($subtotal, $shippingFeeAfterXtra, $req->user());
                if ($valid) {
                    $voucherShippingDiscount = (float) $shipCoupon->calculateDiscount($subtotal, $shippingFeeAfterXtra);
                    $validShipCoupon = $shipCoupon;
                }
            }
        }

        $totalShippingDiscount = $xtraShippingDiscount + $voucherShippingDiscount;
        $finalShippingFee = max(0, $baseShippingFee - $totalShippingDiscount);
        $grandTotal = max(0, $subtotal - $orderDiscount + $finalShippingFee);

        $order = DB::transaction(function() use (
            $cart, $req, $province, $paymentMethod, $selectedService,
            $subtotal, $orderDiscount, $couponCode, $shippingCouponCode,
            $baseShippingFee, $totalShippingDiscount, $finalShippingFee, $grandTotal,
            $validCoupon, $validShipCoupon
        ) {
            // 0. Khoá dòng biến thể và kiểm tra tồn kho lần cuối (chống oversell khi
            //    nhiều khách đặt cùng lúc). Nếu không đủ → rollback toàn bộ đơn.
            $lockedVariants = [];

            foreach ($cart->mucs as $it) {
                if (!$it->variant_id) {
                    continue;
                }

                $variant = BienTheSanPham::whereKey($it->variant_id)
                    ->lockForUpdate()
                    ->first();

                $available = (int) ($variant->stock_qty ?? 0);

                if ($available < $it->qty) {
                    throw ValidationException::withMessages([
                        'cart' => ($it->sanPham->name ?? 'Sản phẩm')." chỉ còn {$available} sản phẩm trong kho.",
                    ]);
                }

                $lockedVariants[$it->variant_id] = $variant;
            }

            // 1. Lưu địa chỉ giao hàng
            $address = DiaChi::create([
                'user_id' => $req->user()?->id,
                'name' => $req->name,
                'phone' => $req->phone,
                'province' => $province,
                'district' => '',
                'ward' => '',
                'address_line' => $req->address,
                'is_default' => false,
            ]);

            // 2. Tạo đơn hàng
            $order = DonHang::create([
                'user_id' => $req->user()?->id,
                'code' => 'OD'.Str::upper(Str::random(8)),
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'shipping_status' => 'unfulfilled',
                'shipping_carrier' => $selectedService['carrier'],
                'shipping_service' => $selectedService['name'],
                'shipping_address_id' => $address->id,
                'billing_address_id' => $address->id,
                'currency' => 'VND',
                'subtotal' => $subtotal,
                'discount_total' => $orderDiscount,
                'coupon_code' => $validCoupon ? $validCoupon->code : null,
                'shipping_coupon_code' => $validShipCoupon ? $validShipCoupon->code : null,
                'shipping_total' => $baseShippingFee,
                'shipping_discount' => $totalShippingDiscount,
                'grand_total' => $grandTotal,
            ]);

            // 3. Tạo thông tin vận chuyển
            VanChuyen::create([
                'order_id' => $order->id,
                'carrier' => $selectedService['carrier'],
                'service' => $selectedService['name'],
                'tracking_code' => 'SPX' . Str::upper(Str::random(9)),
                'status' => 'pending',
                'fee' => $finalShippingFee,
                'label_url' => null,
            ]);

            // 4. Ghi nhận bản ghi thanh toán
            ThanhToan::create([
                'order_id' => $order->id,
                'provider' => $paymentMethod,
                'transaction_id' => null,
                'status' => 'pending',
                'amount' => $order->grand_total,
                'payload' => [
                    'payment_method' => $paymentMethod,
                    'created_by_ip' => $req->ip(),
                ],
            ]);

            // 5. Lưu chi tiết từng sản phẩm và trừ tồn kho
            foreach ($cart->mucs as $it) {
                MucDonHang::create([
                    'order_id' => $order->id,
                    'product_id' => $it->product_id,
                    'variant_id' => $it->variant_id,
                    'sku' => $it->bienThe?->sku ?? 'N/A',
                    'name_snapshot' => $it->sanPham->name,
                    'price' => $it->unit_price,
                    'qty' => $it->qty,
                    'subtotal' => $it->line_total,
                ]);

                if ($it->variant_id && isset($lockedVariants[$it->variant_id])) {
                    $lockedVariants[$it->variant_id]->decrement('stock_qty', $it->qty);
                } elseif ($it->bienThe) {
                    $it->bienThe->decrement('stock_qty', $it->qty);
                }
            }

            // 6. Tăng số lần sử dụng của Voucher nếu có
            if ($validCoupon) {
                $validCoupon->increment('used');
            }
            if ($validShipCoupon) {
                $validShipCoupon->increment('used');
            }

            // 7. Xóa sạch giỏ hàng
            $cart->mucs()->delete();
            $cart->recalc();

            return $order;
        });

        // Nếu chọn quét mã QR VietQR -> chuyển hướng sang trang thanh toán QR
        if ($paymentMethod === 'vietqr') {
            return redirect()->route('checkout.payment.qr', $order->code)
                ->with('info', 'Đơn hàng đã tạo thành công! Vui lòng quét mã QR bên dưới để thanh toán.');
        }

        return redirect()->route('checkout.success', $order->code)
            ->with('success', 'Đặt hàng thành công! Đơn hàng sẽ được thanh toán khi nhận hàng (COD).');
    }

    /**
     * Màn hình thanh toán chuyển khoản Quét mã VietQR
     */
    public function qrPayment($code)
    {
        $order = DonHang::with(['diaChi', 'mucs.sanPham', 'mucs.bienThe', 'payment', 'shipment'])
            ->where('code', $code)
            ->firstOrFail();

        $this->authorizeOrderAccess($order);

        if ($order->payment_status === 'paid') {
            return redirect()->route('checkout.success', $order->code)
                ->with('info', 'Đơn hàng này đã được thanh toán thành công trước đó.');
        }
        $bankId = Setting::get('vietqr.bank_id', config('payment.vietqr.bank_id', 'MB'));
        $bankName = Setting::get('vietqr.bank_name', config('payment.vietqr.bank_name', 'MB Bank (Quân Đội)'));
        $accountNo = Setting::get('vietqr.account_no', config('payment.vietqr.account_no', '0988889999'));
        $accountName = Setting::get('vietqr.account_name', config('payment.vietqr.account_name', 'HIHI SHOP'));
        $template = Setting::get('vietqr.template', config('payment.vietqr.template', 'compact2'));

        $amount = (int) $order->grand_total;
        $memo = 'HIHI ' . $order->code;

        $encodedMemo = urlencode($memo);
        $encodedAccountName = urlencode($accountName);
        $qrImageUrl = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-{$template}.png?amount={$amount}&addInfo={$encodedMemo}&accountName={$encodedAccountName}";

        return view('checkout.qr', compact(
            'order',
            'qrImageUrl',
            'bankId',
            'bankName',
            'accountNo',
            'accountName',
            'memo',
            'amount'
        ));
    }

    /**
     * API kiểm tra trạng thái thanh toán thời gian thực (Polling Ajax)
     */
    public function checkStatus($code)
    {
        $order = DonHang::where('code', $code)->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Đơn hàng không tồn tại.'], 404);
        }

        $this->authorizeOrderAccess($order);

        return response()->json([
            'success' => true,
            'code' => $order->code,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'is_paid' => $order->payment_status === 'paid',
            'redirect_url' => route('checkout.success', $order->code),
        ]);
    }

    /**
     * Mô phỏng xác nhận thanh toán (Dành cho kiểm thử Test Dev hoặc nút "Tôi đã chuyển khoản")
     */
    public function simulatePayment(Request $req, $code)
    {
        $order = DonHang::where('code', $code)->firstOrFail();

        $this->authorizeOrderAccess($order);

        // Chỉ cho phép mô phỏng xác nhận thanh toán ở môi trường dev hoặc bởi Admin.
        // Trên production, khách không được tự đánh dấu đơn là "đã thanh toán".
        abort_unless(
            app()->environment('local') || Auth::user()?->hasRole('Admin'),
            403,
            'Chức năng mô phỏng thanh toán chỉ dùng cho môi trường phát triển.'
        );

        $order->update([
            'payment_status' => 'paid',
            'status' => 'processing',
        ]);

        $payment = ThanhToan::where('order_id', $order->id)->latest()->first();
        if ($payment) {
            $payment->update([
                'status' => 'paid',
                'transaction_id' => 'TRANS_' . strtoupper(Str::random(10)),
                'payload' => array_merge($payment->payload ?? [], [
                    'simulated' => true,
                    'paid_at' => now()->toIso8601String(),
                ]),
            ]);
        }

        if ($req->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Xác nhận thanh toán thành công!',
                'redirect_url' => route('checkout.success', $order->code),
            ]);
        }

        return redirect()->route('checkout.success', $order->code)
            ->with('success', 'Xác nhận thanh toán đơn hàng thành công!');
    }

    /**
     * Webhook Endpoint nhận tín hiệu từ Ngân Hàng / PayOS / Casso / SeAPay
     */
    public function handleWebhook(Request $req)
    {
        $secret = (string) config('payment.webhook.secret', '');

        // Nếu có cấu hình secret thì bắt buộc kiểm tra chữ ký HMAC-SHA256 của raw body.
        if ($secret !== '') {
            $headerName = (string) config('payment.webhook.header', 'X-Webhook-Signature');
            $provided = preg_replace('/^sha256=/i', '', (string) $req->header($headerName, ''));
            $expected = hash_hmac('sha256', $req->getContent(), $secret);

            if ($provided === '' || !hash_equals($expected, $provided)) {
                Log::warning('Payment webhook: chữ ký không hợp lệ', [
                    'ip' => $req->ip(),
                    'header' => $headerName,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid signature',
                ], 401);
            }
        }

        $payload = $req->all();
        $description = $payload['description'] ?? $payload['content'] ?? ($payload['data']['description'] ?? '');
        $amount = $payload['amount'] ?? ($payload['data']['amount'] ?? 0);
        $transactionId = $payload['reference'] ?? $payload['transactionId'] ?? Str::random(12);

        if (preg_match('/OD[A-Z0-9]{8}/i', $description, $matches)) {
            $orderCode = strtoupper($matches[0]);
            $order = DonHang::where('code', $orderCode)->first();

            if ($order && $order->payment_status !== 'paid') {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'processing',
                ]);

                ThanhToan::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'provider' => 'vietqr',
                        'transaction_id' => $transactionId,
                        'status' => 'paid',
                        'amount' => $amount ?: $order->grand_total,
                        'payload' => $payload,
                    ]
                );

                return response()->json(['success' => true, 'message' => "Order {$orderCode} marked as paid"]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Webhook received but no matching pending order']);
    }

    /**
     * Khách báo "Tôi đã chuyển khoản" — KHÔNG tự đánh dấu đã trả tiền,
     * chỉ ghi nhận để admin kiểm tra và xác nhận.
     */
    public function reportTransfer(Request $request, $code)
    {
        $order = DonHang::where('code', $code)->firstOrFail();

        $this->authorizeOrderAccess($order);

        if ($order->payment_status === 'paid') {
            return response()->json([
                'success' => true,
                'already_paid' => true,
                'message' => 'Đơn hàng đã được thanh toán.',
            ]);
        }

        if (!$order->customer_reported_paid_at) {
            $order->update(['customer_reported_paid_at' => now()]);
        }

        return response()->json([
            'success' => true,
            'already_paid' => false,
            'message' => 'Đã ghi nhận. Shop sẽ kiểm tra và xác nhận trong 1–2 phút.',
        ]);
    }

    public function success($code)
    {
        $order = DonHang::with(['diaChi', 'mucs.sanPham', 'mucs.bienThe', 'payment', 'shipment'])
            ->where('code', $code)
            ->firstOrFail();

        $this->authorizeOrderAccess($order);

        return view('orders.success', compact('order'));
    }

    /**
     * Chỉ chủ đơn hoặc Admin được xem/thao tác trên đơn hàng.
     * Trang xác nhận đơn và mã QR có thể chứa thông tin cá nhân nên phải chặn truy cập chéo.
     */
    private function authorizeOrderAccess(DonHang $order): void
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Vui lòng đăng nhập để xem đơn hàng.');
        }

        $isOwner = $order->user_id !== null && (int) $order->user_id === (int) $user->id;

        if (!$isOwner && !$user->hasRole('Admin')) {
            abort(403, 'Bạn không có quyền truy cập đơn hàng này.');
        }
    }
}
