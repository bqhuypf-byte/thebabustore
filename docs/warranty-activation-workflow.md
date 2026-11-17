# Kích hoạt bảo hành

## Vận hành

- Admin → Đơn hàng → **Tạo đơn offline / bảo hành**. Nhập SĐT, tên (tùy chọn), ngày mua, phiên bản và số lượng. Không tạo tài khoản khách và không tự xác nhận thanh toán.
- Mở đơn → **Kích hoạt bảo hành**: xác nhận ngày mua, chọn mặt hàng. Hoặc **Tạo / cấp lại link** để khách tự xác nhận. Nút link chỉ hiện khi đơn có ngày mua và chưa hủy.
- Link dùng đường dẫn `/kich-hoat/{mã đơn}?token=…`, hiệu lực 30 ngày. Cấp lại sẽ vô hiệu link trước. Chỉ lưu hash token trong DB. Nhân viên sao chép tin nhắn trong modal để tự gửi; không có cổng SMS/Zalo mới.
- `/kich-hoat`: nhập mã đơn và SĐT mua hàng để xác minh trước khi hiện mặt hàng. Link token hợp lệ nạp sẵn đơn, cho phép cập nhật số nhận bảo hành; không sửa thông tin gốc của đơn.
- **Mất mã đơn**: tìm sản phẩm → gửi yêu cầu chờ duyệt. Admin → **Yêu cầu xác minh bảo hành** → xác minh bằng chứng, nhập ngày mua và căn cứ, duyệt hoặc từ chối. Chỉ khi duyệt mới tạo đơn offline và kích hoạt trong cùng transaction.
- Admin → **Bảo hành đã kích hoạt**: tìm theo SĐT/mã đơn/sản phẩm, lọc hiệu lực/nguồn kích hoạt; mở đơn để cấp lại link.

## Quy tắc dữ liệu

- Hạn mới = ngày mua được xác nhận + `product_variants.warranty_days`. Không suy đoán ngày mua cho đơn cũ; không tự gán 365 khi số ngày trống/0.
- Không sửa hàng loạt hạn bảo hành cũ. Hạn cũ có ý nghĩa được giữ nguyên khi bổ sung bản ghi kích hoạt.
- Một bản ghi theo đơn + biến thể, có tổng số lượng. Lặp yêu cầu trả lại bản ghi đã có và không gia hạn. Serial/định danh là trường tùy chọn của bản ghi này, không phải hệ thống quản lý serial từng chiếc.
- Khóa đơn trong transaction và unique index `warranty_order_variant_unique` chống tạo trùng. Mặt hàng không thuộc đơn bị từ chối. Lỗi một mặt hàng sẽ hoàn tác toàn bộ lượt chọn.
- Ngày mua sau khi đã có kích hoạt không sửa qua form admin. Đơn hủy không được cấp link/kích hoạt. Hạn đã hết vẫn có thể được ghi nhận nhưng không bị kéo dài từ ngày kích hoạt.
- Tra cứu và xử lý báo lỗi/RMA cũ giữ nguyên; tra cứu SĐT bổ sung số nhận bảo hành đã lưu trong bản ghi kích hoạt. Component kích hoạt cũ dùng chung luồng xác minh, không còn tự tạo bảo hành từ SKU.
- Không thêm ảnh hóa đơn, QR, gateway hoặc quy tắc tự kích hoạt khi giao hàng mới.

## Cài đặt và kiểm tra

Chạy `php artisan migrate` khi triển khai. Hai migration bổ sung dữ liệu và cho phép `orders.user_id`, `order_items.warranty` trống cho đơn chưa kích hoạt; không gán dữ liệu giả vào các dòng cũ.

`php artisan test --filter="WarrantyActivationServiceTest|WarrantyCustomerActivationTest|WarrantyWorkflowTest|OrderReceiptTest|CheckoutOrderTest"`

Các test ghi dữ liệu dùng transaction hoàn tác. Trang công khai giữ ba URL `/tra-cuu-bao-hanh`, `/orders`, `/kich-hoat` và `/kich-hoat/{code}`. Giao diện dùng xanh BABU và Roboto.
