<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cấu hình thanh toán VietQR (NAPAS 247)
    |--------------------------------------------------------------------------
    |
    | Hỗ trợ tạo mã QR động tự động điền Số tài khoản, Số tiền, và Nội dung
    | cho mọi ứng dụng ngân hàng tại Việt Nam (MB, Vietcombank, Techcombank...).
    |
    */
    'vietqr' => [
        'bank_id' => env('VIETQR_BANK_ID', 'MB'), // Mã ngân hàng: MB, VCB, TCB, ACB, VPB, BIDV, ICB, TPB...
        'bank_name' => env('VIETQR_BANK_NAME', 'MB Bank (Quân Đội)'),
        'account_no' => env('VIETQR_ACCOUNT_NO', '0988889999'),
        'account_name' => env('VIETQR_ACCOUNT_NAME', 'HIHI SHOP FASHION'),
        'template' => env('VIETQR_TEMPLATE', 'compact2'), // compact, compact2, qr_only, print
    ],

    /*
    |--------------------------------------------------------------------------
    | Cấu hình Cổng tự động PayOS (Nếu tích hợp Webhook Realtime)
    |--------------------------------------------------------------------------
    */
    'payos' => [
        'client_id' => env('PAYOS_CLIENT_ID', ''),
        'api_key' => env('PAYOS_API_KEY', ''),
        'checksum_key' => env('PAYOS_CHECKSUM_KEY', ''),
    ],
];
