# Đối chiếu tính năng trang sản phẩm

Nguồn: mẫu HTML KZ ZSN ngày 06/11/2026, `website-feature-audit.md`, `workflow-thebabustore.md`, dữ liệu và mã local. Phông chữ giữ theo yêu cầu trực tiếp của chủ cửa hàng. Hai file spec riêng tên storefront/admin không được tìm thấy trong workspace; hai tài liệu hiện có là nguồn đối chiếu thay thế, không coi là tương đương hoàn toàn.

| Tính năng | Nguồn | Phân loại | Quyết định |
|---|---|---|
| Ảnh chính, ảnh thu nhỏ, nút chuyển ảnh | Cả hai | KEEP | Dùng ảnh thực từ sản phẩm và biến thể. |
| Tên, giá, SKU, tồn kho | Cả hai | ADAPT | Lấy giá và trạng thái thật của biến thể, không lấy từ demo. |
| Nhóm phân loại tuần tự | Demo và local | ADAPT | Nhóm do admin nhập; hỗ trợ nhóm nhận diện được từ tên cũ; không tạo tổ hợp ảo. |
| Thay đổi lựa chọn phụ khi tổ hợp cũ không tồn tại | Demo | ADAPT | Chọn phiên bản còn hàng gần nhất và thông báo khi thay đổi lựa chọn khác. |
| Mặc định bán chạy | Demo task | ASK | Chưa có dữ liệu đánh dấu bán chạy/mặc định trong admin. |
| Số lượng tồn chính xác, trừ tồn | Demo task | ASK | Hệ thống chỉ có trạng thái còn/hết; chưa có số lượng tồn, không tự suy đoán hay trừ giả. |
| Số lượng, thêm giỏ, mua ngay | Cả hai | KEEP | Giữ luồng thật, kiểm tra trạng thái và giá phía máy chủ. |
| Phiên bản và SKU trong giỏ, đơn, phiếu in | Cả hai | ADAPT | Dùng dữ liệu thực của biến thể và ID đã lưu trong dòng đơn. |
| Điểm nổi bật và khối mô tả có cấu trúc | Demo | ASK | Cần dữ liệu nội dung và trường quản trị; sản phẩm hiện dùng mô tả HTML hiện có. |
| Bảng so sánh model và thông số cấu trúc | Demo | ASK | Chưa có bảng thông số cấu trúc theo phiên bản trong cơ sở dữ liệu. |
| Tab mô tả, thông số, bảo hành, đánh giá | Cả hai | ADAPT | Lấy dữ liệu đã có; không bịa thông số, chính sách hay đánh giá. |
| Giá, đánh giá, cam kết và phí giao hàng mẫu | Demo | DROP | Không sao chép dữ liệu hoặc cam kết chưa xác nhận. |
| Nút liên hệ phải | Cả hai | ADAPT | Dùng hotline, Messenger, Zalo, giờ hỗ trợ từ cài đặt; không thay bằng số demo. |
| Thanh mua nhanh mobile | Cả hai | KEEP | Dùng cùng phiên bản và số lượng đang chọn. |
| Thanh toán MoMo/ATM/Visa | Demo | DROP | Luồng local hiện chỉ cho COD và chuyển khoản ngân hàng. |
| SEO sản phẩm | Local | KEEP | Dùng tiêu đề, mô tả và ảnh sản phẩm; không sao chép nội dung mẫu. |

Các mục ASK cần dữ liệu hoặc quyết định kinh doanh trước khi triển khai đầy đủ; không coi là hoàn tất.
