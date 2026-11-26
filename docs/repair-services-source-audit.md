# Babu Care — nguồn nội dung và giá

Ngày: 25/11/2026. Bản triển khai local tại `/sua-chua-gear`.

## Phạm vi

Đọc danh mục RightFix trang 1–3 trong lần chuẩn bị workflow; trang 1 và 3 có kiểm tra trình duyệt. Lần triển khai này đọc thêm hai trang chi tiết qua công cụ web:

- https://rightfix.com.vn/thay-switch-chuot-rightfix — giá thẻ 90.000đ; chi tiết có mức cho hai switch 130.000đ. Đây là giá dịch vụ của nguồn, không phải giá linh kiện rời/BABU.
- https://rightfix.com.vn/thay-cuon-chuot-rightfix — 100.000đ, nguồn nói thay đổi theo dòng chuột. Không dùng làm giá cố định trên trang BABU.

Bảng 25 mục đã gộp nằm trong `repair-services-codex-workflow.md`. Nội dung BABU được giữ và bổ sung các triệu chứng từ yêu cầu chủ dự án, thành 35 mục thuộc bốn nhóm hiện có. Nhóm bảo dưỡng bàn phím dùng chung bộ lọc Bàn phím. Tay cầm chưa được xác nhận năng lực tiếp nhận nên giữ FAQ liên hệ, chưa thêm dịch vụ công khai mới.

## Nghiên cứu linh kiện

Đã tìm kiếm Google qua công cụ web với từ khóa switch chuột Kailh/Huano và encoder; mở trang bán trực tiếp. Dữ liệu máy đọc trong `repair-parts-price-research.json`.

| Nguồn | Quan sát | Cách dùng |
| --- | --- | --- |
| [LinkPC Gaming](https://linkpcgaming.com/san-pham/cuon-chuot-scroll-encoder/nut-cuon-chuot-huano-xanh-10mm-nhieu-do-cao-chinh-hang-cuon-lan-chuot-2513.html) | Encoder Huano xanh, tiêu đề 10mm; 10.500đ; mô tả giá một cái, cần xác định chiều cao | Hiển thị giá thị trường một nguồn, chưa gồm công; không cam kết tồn kho BABU |
| [Zpro Kailh](https://zpro.vn/switch-chuot-kailh-gm-8-0-black-mamba-80-trieu-lan-bam/p2384.html) | Black Mamba, 20.000đ, nguồn báo hết hàng, đơn vị chưa ghi rõ | Không xuất bản số giá; liên hệ xác nhận |
| [Zpro Huano](https://zpro.vn/switch-chuot-huano-pink-80m-80-trieu-lan-bam/p2383.html) | Pink 80M, 20.000đ, nguồn báo hết hàng, đơn vị chưa ghi rõ | Không xuất bản số giá; liên hệ xác nhận |

Chưa có hai nguồn độc lập xác nhận cùng biến thể. Công cụ web có thể trả bản lưu cũ; ngày đối chiếu là ngày truy cập, không phải cam kết giá tồn tại tại mọi thời điểm. Không áp dụng giá tổng hợp sàn, voucher hay giá gói sửa chữa làm giá linh kiện. Switch bàn phím và đệm tai còn phụ thuộc model, chưa công bố số tiền. Không tự chế giá sensor, pin hoặc chi phí sửa mạch.

## Giới hạn

Không có phiên đăng nhập RightFix được cấp; dữ liệu đã dùng đều công khai. Chưa khảo sát mọi trang chi tiết, không sao chép hình ảnh/quy định bảo hành của đối thủ. Ảnh minh họa là banner danh mục BABU hiện có, không mô tả là ảnh đã sửa thiết bị.

## Chỉnh sửa admin

Cài đặt Website → Babu Care. Nội dung nhóm, hạng mục, triệu chứng, loại nhu cầu và danh sách linh kiện là JSON cấu trúc trong hai cột mới `settings.repair_services`, `settings.repair_parts`. Khi chưa lưu, dùng bộ mặc định, giữ nguyên dữ liệu cũ trong mã nguồn để tham khảo lịch sử Git. Không thay đổi sản phẩm/tồn kho/giá hàng hóa.

Giá chỉ render khi có cờ kiểm chứng, số dương, ngày đối chiếu và URL http/https hợp lệ. Mọi dịch vụ vẫn báo giá sau kiểm tra. Admin không nhập giá sửa mạch vào linh kiện. Nội dung của các trang khác trong SettingResource được giữ nguyên.
## Cập nhật sau phản hồi thiết kế

Đã cào sâu các trang chi tiết hàn, rã hàn, thay switch MX/Romer-G, LED, mạch, cổng, pin, dây bàn phím; switch, cuộn, dây, pin, cổng và mạch chuột. Các URL nằm ngay trong `resources/data/repair-services.json`. Mạch và cổng chuột bị trang xác minh ở công cụ web nhưng đã đọc được trên trình duyệt: giá nguồn lần lượt 80.000đ và 120.000đ; không chuyển giá sửa mạch thành báo giá cố định BABU.

Bổ sung nguồn dịch vụ [MKShop](https://mkshop.vn/dich-vu-sua-chua-ban-phim-co-chuot-uy-tin-chuyen-nghiep-lay-ngay-tai-ha-noi-mkshop/) cho phạm vi chập cháy, và [Dolozi](https://dolozistore.com/sua-chuot-gaming/) cho nhóm sensor/nút giữa. Chỉ tham khảo phân loại triệu chứng, không sao chép cam kết hoặc bảo hành.

Ảnh bảng giá do chủ dự án gửi trong hội thoại được ưu tiên cho các biến thể chuột: switch đầu 90.000đ, switch tiếp theo 40.000đ; encoder 100.000/150.000đ tùy dòng; feet từ 30.000đ; vệ sinh từ 80.000đ; dây cao su 150.000đ và dây bọc dù 170.000đ. Giá sửa mạch trong ảnh vẫn chỉ lưu nghiên cứu theo yêu cầu báo sau kiểm tra ban đầu.

Bảng bàn phím: hàn 150.000/200.000/250.000đ, rã hàn 200.000/250.000/300.000đ và combo 300.000/400.000/500.000đ theo layout. Switch MX loại thường 70.000đ nút đầu và 20.000đ nút tiếp theo; loại tốt 90.000/40.000đ. Nguồn Romer-G có số không nhất quán: tiêu đề 90.000đ, bảng chung 100.000đ; bản thử dùng giá trang riêng 90.000đ, giữ ghi chú điều kiện trong bảng giá. Không coi đây là giá bán đã cam kết của BABU.

Theo phản hồi mới, bỏ bảng nghiên cứu linh kiện và quy trình khỏi giao diện; giữ banner đầu, lưới ảnh/tên/giá và bảng giá trong chi tiết. Bộ dữ liệu nguồn và ghi chú nghiên cứu vẫn được giữ để chỉnh sửa. Có 21 ảnh công khai tải về `public/images/repair/reference/`, giữ watermark, lưu URL trong dữ liệu; ảnh gán lại theo nhóm nếu không có ảnh riêng. Bản hiện tại có 44 hạng mục. Giá, biến thể và ảnh được chỉnh qua trang admin Babu Care riêng `/hg-digital/repair-catalog`, thay cho tab trong Cài đặt Website mô tả ở bản đầu của tài liệu.