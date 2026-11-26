<?php

namespace App\Support;

class InformationPages
{
    public static function all(): array
    {
        return [
            'about' => 'Giới thiệu',
            'contact' => 'Liên hệ với chúng tôi',
            'chinh-sach-thanh-toan' => 'Chính sách thanh toán',
            'chinh-sach-van-chuyen' => 'Chính sách vận chuyển',
            'chinh-sach-bao-mat' => 'Chính sách bảo mật',
            'chinh-sach-bao-hanh' => 'Chính sách bảo hành',
            'chinh-sach-doi-tra-hang-hoan-tien' => 'Chính sách đổi trả hàng hoàn tiền',
        ];
    }
}
