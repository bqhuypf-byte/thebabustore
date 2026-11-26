<?php

namespace App\Support;

class RepairServicesContent
{
    public static function defaults(): array
    {
        return json_decode(file_get_contents(resource_path('data/repair-services.json')), true, 512, JSON_THROW_ON_ERROR);
    }

    public static function groups(): array
    {
        $groups = \App\Models\Setting::query()->value('repair_services');
        $groups = is_string($groups) ? json_decode($groups, true) : $groups;
        return array_map(static function (array $group) {
            $group['device'] = $group['id'] === 'cleaning' ? 'keyboard' : $group['id'];
            $group['items'] = array_map(static fn (array $item) => array_merge($item, [
                'kind' => $item['kind'] ?? 'repair', 'symptoms' => $item['symptoms'] ?? '', 'scope' => $item['scope'] ?? '',
                'price_from' => null, 'price_note' => 'Báo giá sau kiểm tra', 'warranty' => null,
                'display_price' => ($item['kind'] ?? 'repair') !== 'repair' && is_numeric($item['reference_price_vnd'] ?? null) && $item['reference_price_vnd'] > 0 && filter_var($item['source_url'] ?? '', FILTER_VALIDATE_URL) ? (int) $item['reference_price_vnd'] : null,
            ]), $group['items'] ?? []);
            return $group;
        }, is_array($groups) ? $groups : self::defaults());
    }

    public static function partDefaults(): array
    {
        return [
            ['name' => 'Encoder Huano xanh', 'variant' => '10mm', 'device' => 'mouse', 'price' => 10500, 'unit' => 'cái', 'verified' => true, 'checked_at' => '2026-11-25', 'source_name' => 'LinkPC Gaming', 'source_url' => 'https://linkpcgaming.com/san-pham/cuon-chuot-scroll-encoder/nut-cuon-chuot-huano-xanh-10mm-nhieu-do-cao-chinh-hang-cuon-lan-chuot-2513.html', 'note' => 'Một nguồn bán. Cần đo đúng chiều cao encoder; chưa bao gồm công thay.'],
            ['name' => 'Switch Kailh GM 8.0', 'variant' => 'Black Mamba', 'device' => 'mouse', 'price' => null, 'unit' => 'cái', 'verified' => false, 'checked_at' => '2026-11-25', 'source_name' => 'Zpro', 'source_url' => 'https://zpro.vn/switch-chuot-kailh-gm-8-0-black-mamba-80-trieu-lan-bam/p2384.html', 'note' => 'Nguồn báo hết hàng; liên hệ xác nhận giá và linh kiện phù hợp.'],
            ['name' => 'Switch Huano Pink', 'variant' => '80M', 'device' => 'mouse', 'price' => null, 'unit' => 'cái', 'verified' => false, 'checked_at' => '2026-11-25', 'source_name' => 'Zpro', 'source_url' => 'https://zpro.vn/switch-chuot-huano-pink-80m-80-trieu-lan-bam/p2383.html', 'note' => 'Nguồn báo hết hàng; liên hệ xác nhận giá và linh kiện phù hợp.'],
            ['name' => 'Switch bàn phím', 'variant' => 'Theo loại switch và socket', 'device' => 'keyboard', 'price' => null, 'unit' => 'cái', 'verified' => false, 'note' => 'Cần xác định model và loại switch trước khi báo giá.'],
            ['name' => 'Đệm tai / đệm đầu', 'variant' => 'Theo model tai nghe', 'device' => 'headphones', 'price' => null, 'unit' => 'bộ', 'verified' => false, 'note' => 'Kích thước và ngàm lắp được xác nhận theo thiết bị.'],
        ];
    }

    public static function parts(): array
    {
        $parts = \App\Models\Setting::query()->value('repair_parts');
        $parts = is_string($parts) ? json_decode($parts, true) : $parts;
        return array_map(static function (array $part) {
            $url = $part['source_url'] ?? '';
            $valid = filter_var($url, FILTER_VALIDATE_URL) && in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https']);
            $part['source_url'] = $valid ? $url : null;
            if (! ($part['verified'] ?? false) || ! $valid || empty($part['checked_at']) || ! is_numeric($part['price'] ?? null) || $part['price'] <= 0) {
                $part['price'] = null;
            }
            return $part;
        }, is_array($parts) ? $parts : self::partDefaults());
    }

    public static function faqs(): array
    {
        return [
            ['Sửa chuột mất bao lâu?', 'Babu xác nhận thời gian sau khi kiểm tra lỗi và linh kiện phù hợp. Bạn có thể mô tả tình trạng trong form để được tư vấn trước.'],
            ['Sửa tai nghe mất bao lâu?', 'Thời gian phụ thuộc tình trạng tai nghe và linh kiện cần thay. Babu sẽ trao đổi phương án và thời gian dự kiến trước khi sửa.'],
            ['Babu có nhận sửa tay cầm chơi game không?', 'Trang hiện tập trung vào bàn phím, chuột và tai nghe. Nếu cần hỗ trợ tay cầm, vui lòng liên hệ để Babu xác nhận khả năng tiếp nhận trước.'],
            ['Có báo giá trước khi sửa không?', 'Babu kiểm tra tình trạng, đề xuất phương án và báo giá. Chỉ thực hiện sau khi bạn đồng ý với phương án sửa chữa.'],
            ['Linh kiện cũ thay ra có được trả lại không?', 'Bạn có thể yêu cầu nhận lại linh kiện cũ khi bàn giao. Hãy trao đổi với Babu lúc tiếp nhận để ghi rõ yêu cầu.'],
            ['Bảo hành sau sửa như thế nào?', 'Thời hạn và phạm vi bảo hành được xác nhận theo từng hạng mục trước khi sửa. Vui lòng liên hệ xác nhận; trang chưa công bố thời hạn cố định.'],
            ['Có thể gửi thiết bị từ xa không?', 'Vui lòng liên hệ trước để Babu xác nhận cách tiếp nhận phù hợp. Không gửi thiết bị khi chưa thống nhất với shop.'],
            ['Sau khi sửa tai nghe cần lưu ý gì?', 'Giữ tai nghe khô ráo, vệ sinh nhẹ nhàng, tránh kéo căng hoặc cuộn dây quá chặt và tránh nơi có nhiệt độ cao.'],
            ['Lube switch có ảnh hưởng bảo hành hãng không?', 'Việc tháo hoặc can thiệp bàn phím có thể ảnh hưởng điều kiện bảo hành của hãng. Babu sẽ trao đổi với bạn trước; nên kiểm tra chính sách của sản phẩm trước khi thực hiện.'],
        ];
    }
}
