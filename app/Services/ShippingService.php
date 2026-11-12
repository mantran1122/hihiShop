<?php

namespace App\Services;

class ShippingService
{
    /**
     * Danh sách 63 Tỉnh/Thành Việt Nam và phân nhóm vùng miền so với kho chính (Hà Nội)
     */
    const ZONE_SAME_CITY = 'same_city';   // Hà Nội (Nội thành kho chính)
    const ZONE_NORTH     = 'north';       // Miền Bắc (Nội miền)
    const ZONE_CENTRAL   = 'central';     // Miền Trung
    const ZONE_SOUTH     = 'south';       // Miền Nam (Liên miền)

    public static function getProvinces(): array
    {
        return [
            // Nội thành kho chính
            'Hà Nội' => self::ZONE_SAME_CITY,

            // Miền Bắc (Nội miền)
            'Bắc Ninh' => self::ZONE_NORTH,
            'Hải Phòng' => self::ZONE_NORTH,
            'Quảng Ninh' => self::ZONE_NORTH,
            'Hưng Yên' => self::ZONE_NORTH,
            'Hải Dương' => self::ZONE_NORTH,
            'Thái Nguyên' => self::ZONE_NORTH,
            'Vĩnh Phúc' => self::ZONE_NORTH,
            'Nam Định' => self::ZONE_NORTH,
            'Thái Bình' => self::ZONE_NORTH,
            'Ninh Bình' => self::ZONE_NORTH,
            'Hà Nam' => self::ZONE_NORTH,
            'Phú Thọ' => self::ZONE_NORTH,
            'Bắc Giang' => self::ZONE_NORTH,
            'Hòa Bình' => self::ZONE_NORTH,
            'Lạng Sơn' => self::ZONE_NORTH,
            'Tuyên Quang' => self::ZONE_NORTH,
            'Yên Bái' => self::ZONE_NORTH,
            'Lào Cai' => self::ZONE_NORTH,
            'Sơn La' => self::ZONE_NORTH,
            'Điện Biên' => self::ZONE_NORTH,
            'Lai Châu' => self::ZONE_NORTH,
            'Hà Giang' => self::ZONE_NORTH,
            'Cao Bằng' => self::ZONE_NORTH,
            'Bắc Kạn' => self::ZONE_NORTH,

            // Miền Trung
            'Đà Nẵng' => self::ZONE_CENTRAL,
            'Thừa Thiên Huế' => self::ZONE_CENTRAL,
            'Thanh Hóa' => self::ZONE_CENTRAL,
            'Nghệ An' => self::ZONE_CENTRAL,
            'Hà Tĩnh' => self::ZONE_CENTRAL,
            'Quảng Bình' => self::ZONE_CENTRAL,
            'Quảng Trị' => self::ZONE_CENTRAL,
            'Quảng Nam' => self::ZONE_CENTRAL,
            'Quảng Ngãi' => self::ZONE_CENTRAL,
            'Bình Định' => self::ZONE_CENTRAL,
            'Phú Yên' => self::ZONE_CENTRAL,
            'Khánh Hòa' => self::ZONE_CENTRAL,
            'Ninh Thuận' => self::ZONE_CENTRAL,
            'Bình Thuận' => self::ZONE_CENTRAL,
            'Kon Tum' => self::ZONE_CENTRAL,
            'Gia Lai' => self::ZONE_CENTRAL,
            'Đắk Lắk' => self::ZONE_CENTRAL,
            'Đắk Nông' => self::ZONE_CENTRAL,
            'Lâm Đồng' => self::ZONE_CENTRAL,

            // Miền Nam (Liên miền)
            'TP. Hồ Chí Minh' => self::ZONE_SOUTH,
            'Bình Dương' => self::ZONE_SOUTH,
            'Đồng Nai' => self::ZONE_SOUTH,
            'Bà Rịa - Vũng Tàu' => self::ZONE_SOUTH,
            'Long An' => self::ZONE_SOUTH,
            'Tiền Giang' => self::ZONE_SOUTH,
            'Bến Tre' => self::ZONE_SOUTH,
            'Trà Vinh' => self::ZONE_SOUTH,
            'Vĩnh Long' => self::ZONE_SOUTH,
            'Đồng Tháp' => self::ZONE_SOUTH,
            'An Giang' => self::ZONE_SOUTH,
            'Kiên Giang' => self::ZONE_SOUTH,
            'Cần Thơ' => self::ZONE_SOUTH,
            'Hậu Giang' => self::ZONE_SOUTH,
            'Sóc Trăng' => self::ZONE_SOUTH,
            'Bạc Liêu' => self::ZONE_SOUTH,
            'Cà Mau' => self::ZONE_SOUTH,
            'Tây Ninh' => self::ZONE_SOUTH,
            'Bình Phước' => self::ZONE_SOUTH,
        ];
    }

    /**
     * Bảng cước vận chuyển chuẩn Shopee Việt Nam (VND)
     */
    public static function getRates(string $province = 'Hà Nội', int $itemCount = 1, float $subtotal = 0): array
    {
        $provinces = self::getProvinces();
        $zone = $provinces[$province] ?? self::ZONE_NORTH;

        // Phụ thu theo số lượng kiện hàng (vượt 3 món phụ thu 4.000đ/món)
        $weightSurcharge = max(0, $itemCount - 3) * 4000;

        // Xác định chính sách Trợ giá vận chuyển tự động (Shopee Freeship Xtra)
        $xtraDiscount = 0;
        $xtraNote = null;
        if ($subtotal >= 450000) {
            $xtraDiscount = 30000;
            $xtraNote = 'Freeship Xtra: Giảm 30.000đ phí ship cho đơn từ 450.000đ';
        } elseif ($subtotal >= 250000) {
            $xtraDiscount = 15000;
            $xtraNote = 'Freeship Xtra: Giảm 15.000đ phí ship cho đơn từ 250.000đ';
        }

        $rates = [];

        // 1. Dịch vụ Hỏa Tốc (Shopee Instant / Grab Express) - Chỉ áp dụng nội thành
        if ($zone === self::ZONE_SAME_CITY) {
            $baseFee = 45000 + $weightSurcharge;
            $finalFee = max(0, $baseFee - $xtraDiscount);
            $rates['instant'] = [
                'id' => 'instant',
                'carrier' => 'Shopee Instant / Grab',
                'name' => 'Hỏa Tốc (1 - 2 giờ)',
                'badge' => 'Giao siêu tốc',
                'time_text' => 'Nhận hàng sau 1 - 2 tiếng',
                'base_fee' => $baseFee,
                'discount' => min($xtraDiscount, $baseFee),
                'final_fee' => $finalFee,
                'icon' => 'bolt',
                'is_available' => true,
            ];
        }

        // 2. Dịch vụ Nhanh (SPX Express / Giao Hàng Nhanh GHN) - Phổ biến nhất
        $baseFast = match($zone) {
            self::ZONE_SAME_CITY => 22000,
            self::ZONE_NORTH     => 28000,
            self::ZONE_CENTRAL   => 32000,
            self::ZONE_SOUTH     => 38000,
            default              => 30000,
        } + $weightSurcharge;

        $timeFast = match($zone) {
            self::ZONE_SAME_CITY => 'Nhận ngày mai (24h)',
            self::ZONE_NORTH     => 'Nhận sau 1 - 2 ngày',
            self::ZONE_CENTRAL   => 'Nhận sau 2 - 3 ngày',
            self::ZONE_SOUTH     => 'Nhận sau 2 - 3 ngày (Đường bay)',
            default              => 'Nhận sau 2 - 3 ngày',
        };

        $finalFast = max(0, $baseFast - $xtraDiscount);
        $rates['fast'] = [
            'id' => 'fast',
            'carrier' => 'SPX Express / GHN',
            'name' => 'Nhanh (Tiêu chuẩn Shopee)',
            'badge' => 'Khuyên dùng',
            'time_text' => $timeFast,
            'base_fee' => $baseFast,
            'discount' => min($xtraDiscount, $baseFast),
            'final_fee' => $finalFast,
            'icon' => 'truck',
            'is_available' => true,
        ];

        // 3. Dịch vụ Tiết Kiệm (SPX Tiết Kiệm / ViettelPost)
        $baseEconomy = match($zone) {
            self::ZONE_SAME_CITY => 16000,
            self::ZONE_NORTH     => 20000,
            self::ZONE_CENTRAL   => 24000,
            self::ZONE_SOUTH     => 28000,
            default              => 22000,
        } + $weightSurcharge;

        $timeEconomy = match($zone) {
            self::ZONE_SAME_CITY => 'Nhận sau 1 - 2 ngày',
            self::ZONE_NORTH     => 'Nhận sau 2 - 3 ngày',
            self::ZONE_CENTRAL   => 'Nhận sau 3 - 4 ngày',
            self::ZONE_SOUTH     => 'Nhận sau 4 - 5 ngày (Đường bộ)',
            default              => 'Nhận sau 3 - 5 ngày',
        };

        $finalEconomy = max(0, $baseEconomy - $xtraDiscount);
        $rates['economy'] = [
            'id' => 'economy',
            'carrier' => 'SPX Tiết Kiệm / ViettelPost',
            'name' => 'Tiết Kiệm',
            'badge' => 'Cước rẻ',
            'time_text' => $timeEconomy,
            'base_fee' => $baseEconomy,
            'discount' => min($xtraDiscount, $baseEconomy),
            'final_fee' => $finalEconomy,
            'icon' => 'box',
            'is_available' => true,
        ];

        return [
            'zone' => $zone,
            'province' => $province,
            'item_count' => $itemCount,
            'weight_surcharge' => $weightSurcharge,
            'xtra_discount' => $xtraDiscount,
            'xtra_note' => $xtraNote,
            'methods' => $rates,
        ];
    }
}

