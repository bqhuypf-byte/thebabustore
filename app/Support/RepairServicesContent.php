<?php

namespace App\Support;

class RepairServicesContent
{
    public static function groups(): array
    {
        $definitions = [
            ['keyboard', 'Thay switch / hotswap', 'Xử lý phím kẹt, chập chờn và thay switch phù hợp với bàn phím.', [
                'Thay switch MX', 'Thay switch Romer-G', 'Hàn switch của khách', 'Rã hàn switch của khách', 'Combo rã và hàn switch',
            ]],
            ['cleaning', 'Vệ sinh & lube bàn phím', 'Làm sạch, chăm switch và stab để cải thiện trải nghiệm gõ.', [
                'Vệ sinh keycap, PCB, plate và case', 'Lube switch', 'Mod stab', 'Mod LED đơn 2 chân', 'Phục hồi keycap, khung và mạch bàn phím', 'Sơn bàn phím theo yêu cầu', 'Kiểm tra bàn phím vào nước, rỉ bề mặt',
            ]],
            ['mouse', 'Sửa chuột', 'Kiểm tra double click, cuộn lăn, dây kết nối và các lỗi thường gặp.', [
                'Thay switch chuột', 'Thay cuộn lăn', 'Thay hoặc nối dây chuột', 'Thay cổng kết nối', 'Thay pin chuột', 'Kiểm tra lỗi mạch chuột', 'Thay feet chuột', 'Vệ sinh chuột',
            ]],
            ['headphones', 'Tai nghe & cáp', 'Kiểm tra âm thanh, dây kết nối và các bộ phận cần thay thế.', [
                'Thay đệm tai nghe', 'Thay đệm quai đeo', 'Thay hoặc nối dây tai nghe', 'Kiểm tra driver và lỗi âm thanh', 'Vệ sinh tai nghe', 'Phục hồi gãy vỡ tai nghe',
            ]],
        ];

        return array_map(static fn (array $group) => [
            'id' => $group[0], 'title' => $group[1], 'description' => $group[2],
            'items' => array_map(static fn (string $name) => [
                'name' => $name, 'price_from' => null, 'price_note' => 'Liên hệ báo giá',
                'warranty' => null, 'source' => 'Tài liệu dịch vụ do chủ shop cung cấp; giá và điều kiện chờ xác nhận.',
            ], $group[3]),
        ], $definitions);
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
