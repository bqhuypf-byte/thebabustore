<?php

namespace App\Support;

use App\Models\Setting;

class HomepageContent
{
    public static function defaults(?Setting $setting): array
    {
        $sections = [];
        $description = trim(strip_tags((string) $setting?->company_description));
        if ($description !== '') {
            $sections[] = [
                'enabled' => true, 'layout' => 'split', 'title' => 'Khám phá tất cả sản phẩm',
                'body' => 'Bàn phím, chuột và phụ kiện cho góc máy của bạn.', 'image' => $setting?->banner_web,
                'button_label' => 'Xem tất cả sản phẩm', 'link' => '/shop', 'full_link' => true,
            ];
        }
        $sections[] = [
            'enabled' => true, 'layout' => 'banner', 'title' => 'BABU HUB',
            'body' => 'Khám phá bàn phím, chuột và phụ kiện cho góc máy của bạn.',
            'image' => $setting?->banner_web, 'button_label' => 'Khám phá sản phẩm', 'link' => '/shop',
        ];
        if (filled($setting?->facebook)) {
            $sections[] = [
                'enabled' => true, 'layout' => 'banner', 'title' => 'Cùng kết nối. Cùng chia sẻ.',
                'body' => 'Kết nối với The BABU Store trên Facebook.',
                'button_label' => 'Ghé thăm Facebook', 'link' => $setting->facebook,
            ];
        }
        $sections[] = [
            'enabled' => true, 'layout' => 'faq', 'title' => 'Bạn cần hỗ trợ? BABU ở đây.',
            'body' => 'Tìm thông tin mua hàng, giao hàng và chính sách của cửa hàng.',
            'items' => [
                ['title' => 'Làm sao để tra cứu đơn hàng?', 'body' => 'Tra cứu thông tin đơn hàng tại trang Tra cứu đơn hàng của cửa hàng.', 'link' => '/orders'],
                ['title' => 'Xem chính sách giao hàng ở đâu?', 'body' => 'Thông tin vận chuyển được đăng tại trang Chính sách vận chuyển.', 'link' => '/chinh-sach-van-chuyen'],
                ['title' => 'Xem điều kiện bảo hành ở đâu?', 'body' => 'Tham khảo điều kiện và hướng dẫn tại trang Chính sách bảo hành.', 'link' => '/chinh-sach-bao-hanh'],
                ['title' => 'Cửa hàng áp dụng đổi trả như thế nào?', 'body' => 'Xem điều kiện áp dụng tại trang Chính sách đổi trả và hoàn tiền.', 'link' => '/chinh-sach-doi-tra-hang-hoan-tien'],
            ],
        ];
        return $sections;
    }
}