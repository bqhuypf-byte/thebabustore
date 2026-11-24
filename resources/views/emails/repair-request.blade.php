<!DOCTYPE html><html lang="vi"><head><meta charset="utf-8"><title>Yêu cầu sửa chữa Babu Care</title></head><body>
<h1>Yêu cầu tư vấn sửa chữa</h1><p>Khách gửi yêu cầu từ trang Babu Care. Đây chưa phải lịch hẹn hay phiếu sửa đã tiếp nhận.</p>
<table cellpadding="8"><tr><th align="left">Họ tên</th><td>{{ $data['name'] }}</td></tr><tr><th align="left">Điện thoại</th><td>{{ $data['phone'] }}</td></tr><tr><th align="left">Thiết bị</th><td>{{ ['keyboard' => 'Bàn phím', 'mouse' => 'Chuột', 'headphones' => 'Tai nghe / cáp'][$data['deviceType']] }}</td></tr></table>
<h2>Mô tả lỗi</h2><p style="white-space:pre-wrap">{{ $data['description'] }}</p>
</body></html>
