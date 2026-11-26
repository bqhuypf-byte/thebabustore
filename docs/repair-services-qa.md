# Babu Care — kiểm tra bản thiết kế cuối

Ngày: 25/11/2026. URL: http://127.0.0.1:8000/sua-chua-gear

## Thiết kế đã chốt theo phản hồi

Giữ banner BABU Care đầu trang. Bên dưới là danh mục 44 mục: 15 sửa/thay linh kiện bàn phím, 6 bảo dưỡng bàn phím, 15 chuột và 8 tai nghe. Lưới 4 cột desktop, 2 cột mobile; 12 thẻ đầu và nút xem thêm. Có lọc thiết bị, tìm không dấu và trạng thái không có kết quả. Bỏ bảng linh kiện rời, khối quy trình, khối tra cứu dài, FAQ và các lời giải thích dài trên storefront.

Dịch vụ tiêu chuẩn hiện giá tham khảo và dropdown biến thể khi có nhiều mức. Click ảnh/tên mở bảng giá theo dịch vụ; nút tư vấn điền đúng thiết bị, hạng mục và biến thể vào form đã có. Mục sửa mạch, chập cháy, sensor vẫn báo giá sau kiểm tra.

## Kết quả

- 15 bài test, 244 assertions: RepairServicesTest, InformationPagesTest, WarrantyWorkflowTest đều qua. Bao gồm validation, mail fake, gửi trùng, lỗi gửi, rate limit, escape dữ liệu khách; giá chưa xác minh và URL không an toàn; lưu nội dung bằng form admin Babu Care rồi hiển thị trên storefront.
- Chromium 1440px: banner hiện, 12 thẻ ban đầu, 4 cột, không tràn ngang.
- Chromium 390px: 2 cột, không tràn ngang. Bảng giá trong dialog không bị tràn ngang.
- Chọn hàn switch 75%–TKL: giá đổi thành 200.000đ, dialog có 3 dòng layout, tiêu đề giá đúng biến thể; form ghi đúng layout đã chọn.
- Tìm sensor: một kết quả. Tìm chuỗi không tồn tại: hiện trạng thái trống. Lọc chuột: 15 mục.
- Escape đóng dialog; focus trả về điểm mở. Không có lỗi JavaScript; không ghi nhận ảnh tải lỗi trong lần kiểm tra.
- Không gửi yêu cầu/mail thật. Không đăng nhập vào tài khoản khách hoặc thay đổi sản phẩm/stock.

Kết quả máy đọc: [browser-results.json](repair-services-qa/browser-results.json).

## Ảnh

- [Banner desktop](repair-services-qa/desktop-hero.png)
- [Danh mục desktop](repair-services-qa/desktop-catalog.png)
- [Bảng giá biến thể desktop](repair-services-qa/desktop-price-variants.png)
- [Banner mobile](repair-services-qa/mobile-hero.png)
- [Danh mục mobile](repair-services-qa/mobile-catalog.png)
- [Bảng giá mobile](repair-services-qa/mobile-price-variants.png)

## Admin và giới hạn

Admin → Quản lý nội dung → Babu Care — Dịch vụ, đường dẫn `/hg-digital/repair-catalog`. Lưu chỉ hai cột nội dung dịch vụ và linh kiện, không ghi các cấu hình khác. Trang riêng tránh phụ thuộc các trường upload cũ trong form Cài đặt Website. Có thể chỉnh dịch vụ, mô tả, giá nguồn, điều kiện và các dòng biến thể. Form admin được test bằng tài khoản thử trong transaction rollback; chưa thao tác trên một phiên admin người dùng đang đăng nhập.

Ảnh RightFix được giữ watermark và ghi rõ là ảnh tham khảo, không phải ảnh sửa thực tế của BABU. Giá từ nguồn và ảnh bảng giá người dùng cung cấp là giá tham khảo; không sao chép bảo hành của đối thủ thành bảo hành BABU. Chưa khảo sát mọi chi tiết tai nghe; các mục tai nghe được giữ từ nội dung trước, không tự gán giá khi chưa đủ căn cứ.