# Khảo sát website The BABU Store

Ngày khảo sát: 05/11/2026. Nguồn: [website mẫu](https://nextjs.hgdigital.vn/) và [trang quản trị](https://nextjs.hgdigital.vn/hg-digital). Mục tiêu: mô tả hành vi và giao diện nhìn thấy để hỗ trợ triển khai trong dự án này.

**Yêu cầu của dự án:** rà từng trang, tương tác và trạng thái của cả website khách và admin; ghi kết quả thực tế, phần chưa kiểm thử và khác biệt so với local. Không đánh dấu hoàn tất chỉ vì đã xem trang hoặc tìm thấy nút trên giao diện.

## Cách đọc và giới hạn

- **Đã thao tác**: đã bấm hoặc chọn và quan sát kết quả trong trình duyệt.
- **Đã xem**: đã mở trang hoặc biểu mẫu và ghi nhận thành phần hiển thị; chưa xác nhận mọi nút hoạt động.
- **Chưa kiểm thử**: chưa quan sát được kết quả hoặc thao tác có thể làm thay đổi dữ liệu thực.
- Các con số, sản phẩm và tồn kho là dữ liệu nhìn thấy tại thời điểm khảo sát; không mặc định là dữ liệu cố định hoặc chính sách của dự án local.
- Khảo sát dùng phiên trình duyệt riêng; đã thêm một sản phẩm vào giỏ của phiên này để xem giỏ và checkout. Không xác nhận đặt hàng thật, chuyển tiền, xóa hoặc sửa dữ liệu admin. Không lưu tài khoản, mật khẩu hoặc dữ liệu khách hàng trong tài liệu này.
- Báo cáo này **chưa phải kiểm thử toàn bộ chức năng**: nhiều biểu mẫu admin mới chỉ được xem, chưa lưu; trải nghiệm sau đặt hàng và các chức năng tích hợp chưa được xác minh.

## Cấu trúc chung

Đã xem header gồm thanh thông báo, logo, menu Sản phẩm, Khuyến mãi, Tra cứu đơn hàng, Hướng dẫn & Tips, tìm kiếm với gợi ý phím tắt, đăng nhập, giỏ hàng và nút đổi sáng/tối. Footer chứa các nhóm mua sắm, tài khoản/đơn hàng và liên kết admin. Link hiển thị và nội dung có thể thay đổi theo cấu hình website.

Giao diện quản trị dùng font **Geist** với fallback hệ thống; khi đo trên màn admin, font thân trang là 13px, line-height 20px, letter-spacing -0,195px. Có chế độ sáng/tối. Trang khách sử dụng bố cục nền sáng, card viền nhạt, bo góc, khoảng trắng lớn và nút tròn. Chưa đo đầy đủ từng breakpoint mobile/tablet.

## Trang khách và luồng đã khảo sát

| Trang | Trạng thái | Thành phần và kết quả quan sát |
| --- | --- | --- |
| [Trang chủ](/) | Đã xem, đã thao tác | Các nhóm danh mục, sản phẩm nổi bật, nút xem tất cả; bấm **Xem nhanh** mở modal không chuyển trang. |
| [Cửa hàng](/products) | Đã thao tác | Danh sách sản phẩm, tìm kiếm, danh mục, khoảng giá và sắp xếp. Bấm Bàn phím cơ → URL thêm `category=ban-phim-co`; chọn giá tăng dần → thêm `sort=price-asc`; chọn dưới 500.000₫ → thêm `price=under-500`. Bấm đặt lại bộ lọc đã bỏ lọc danh mục/giá, vẫn giữ sắp xếp. |
| Chi tiết sản phẩm, ví dụ [/products/ghe-cong-thai-hoc-saigu-ares-l8](/products/ghe-cong-thai-hoc-saigu-ares-l8) | Đã xem | Breadcrumb, ảnh chính và ảnh thu nhỏ, tên, giá, phiên bản/SKU, số lượng, nút giỏ và mua ngay; tab mô tả, thông số, bảo hành, đánh giá; sản phẩm liên quan. Chưa kiểm thử từng tab và mọi biến thể. |
| [Giỏ hàng](/cart) | Đã thao tác một phần | Bấm nút thêm vào giỏ từ danh sách → thông báo thành công; trang giỏ hiển thị sản phẩm, số lượng, đơn giá, xóa từng sản phẩm/xóa tất cả, voucher, tổng kết và nút thanh toán. Chưa bấm xóa hoặc xác nhận mã giảm giá. |
| [Checkout](/checkout) | Đã xem, chưa đặt hàng | Có thể mở trang thanh toán với giỏ của phiên chưa đăng nhập. Biểu mẫu hiển thị họ tên, điện thoại, email, tỉnh/thành, địa chỉ, ghi chú; lựa chọn VietQR, COD, MoMo; tóm tắt sản phẩm, voucher, tổng tiền và nút xác nhận. Chưa xác minh xử lý tiền, phí, coupon, đặt hàng thành công hoặc trạng thái sau thanh toán. |
| [Đơn hàng](/orders) | Đã xem | Có tìm theo mã/tên sản phẩm, làm mới, lọc trạng thái và trạng thái rỗng khi phiên không có đơn. Chưa xác minh tra cứu một đơn thực tế. |
| [Hướng dẫn](/guides) | Đã xem | Tìm bài viết, lọc theo nhóm nội dung, card dẫn đến `/guides/{slug}`. Chưa kiểm thử tất cả bộ lọc và trang chi tiết bài. |
| [Tài khoản](/login) | Đã thao tác một phần | Bấm tab Tạo Tài Khoản → form họ tên, email, điện thoại, mật khẩu; tab Đăng Nhập có email, mật khẩu, nút đăng nhập và tùy chọn điền tài khoản mẫu. Đường dẫn `/register` chuyển về `/login`. Đã thử nút tài khoản mẫu nhưng chưa xác nhận kết quả đăng nhập thành công; chưa tạo tài khoản. |

### Xem nhanh sản phẩm

1. Rê lên card sản phẩm → ảnh bị làm mờ, nút **Xem nhanh** hiện giữa ảnh.
2. Bấm **Xem nhanh** → modal nổi trên nền tối; không điều hướng khỏi danh sách.
3. Modal quan sát được có hai cột trên desktop: ảnh lớn/ảnh thu nhỏ bên trái; tên, giá, lựa chọn biến thể/SKU, nội dung nổi bật, số lượng và hành động giỏ hàng/mua ngay bên phải. Có nút đóng và link tới chi tiết sản phẩm.
4. Trong lần thao tác tiếp theo, bấm ảnh thu nhỏ thứ hai đã đổi ảnh lớn; bấm `+` đổi số lượng từ 1 sang 2; bấm Thêm Vào Giỏ đóng modal và hiện thông báo thêm hai sản phẩm. Chưa kiểm thử tất cả biến thể, phím Escape, quản lý focus và luồng mua ngay trên mọi kích thước màn hình.

### Chi tiết sản phẩm: thao tác bổ sung

- Tại `/products/tai-nghe-kz-saga`, bấm biến thể thứ hai → giá và SKU trên trang đổi tương ứng. Bấm tab Thông số kỹ thuật → nội dung thông số thay thế mô tả; bấm tab Đánh giá → hiện trạng thái đánh giá và form gửi nhận xét. Không gửi đánh giá lên site mẫu.
- Bản local `/product/weikav-nut75` đã được dựng lại theo cấu trúc ảnh lớn, thông tin sản phẩm và tab. Đã bấm biến thể và xác nhận ảnh, giá, SKU đổi; đã bấm tab thông số và thấy nội dung tab thay đổi; tăng số lượng rồi thêm vào giỏ, kiểm tra giỏ hiển thị đúng phiên bản và số lượng. Dữ liệu, hình ảnh và chính sách lấy từ cơ sở dữ liệu local; không mặc định sao chép thông tin cam kết của site mẫu.
- Chưa kiểm thử hết các trường hợp: sản phẩm không có ảnh/biến thể, review gửi và duyệt trong admin, mua ngay/checkout trên trang này, tất cả kích thước màn hình và sản phẩm liên quan.

### Cập nhật local: header, cửa hàng, giỏ hàng

- Các trang đang dùng `inc.header` / `inc.footer`, trang chủ và trang chi tiết đã chuyển sang header/footer chung. Những trang khác có nội dung cũ vẫn cần đối chiếu riêng.
- Cửa hàng local đã có bố cục danh sách và lọc danh mục; đã bấm thử danh mục và thấy số kết quả thay đổi. Bấm xem nhanh mở modal có ảnh phân loại; đã chọn biến thể, thêm vào giỏ và thấy header cập nhật.
- Giỏ local đã chuyển sang bố cục mới. Đã thử tăng số lượng và xác nhận số lượng/tạm tính trong giỏ và header cập nhật tương ứng. Giao diện checkout được thay mới nhưng chưa xác minh toàn bộ tương tác trên trình duyệt.
- Thành phần xem nhanh mới được gắn vào cửa hàng và sản phẩm liên quan trong trang chi tiết; chưa kiểm tra hoặc gắn vào mọi danh sách sản phẩm còn lại. Giỏ/checkout local chưa có đầy đủ voucher như site mẫu, và checkout hiện vẫn yêu cầu đăng nhập.

### Hành trình đơn hàng

Quan sát: danh sách hoặc chi tiết → thêm vào giỏ → giỏ hàng → checkout. Đã bấm thêm sản phẩm và mở được giỏ, checkout. Trong một phiên thử, chọn mã được gợi ý `BABU10` làm giỏ hàng hiển thị số tiền giảm và tổng đã giảm; chuyển sang checkout vẫn hiển thị mã và khoản giảm. Đã bấm chọn COD và MoMo: radio đổi trạng thái, MoMo mở thêm thông tin thanh toán. Đây chỉ là xác nhận trạng thái giao diện; chưa chứng minh mã được áp dụng an toàn ở backend hoặc thanh toán tích hợp thực sự hoạt động. Chưa bấm xác nhận đặt hàng thật; chưa thể khẳng định backend tạo đơn thế nào, thực tế có xuất phiếu hay hóa đơn điện tử, kiểm tra thanh toán tự động, trừ kho, gửi email, hoặc đồng bộ với admin ra sao. Cần kiểm thử trong môi trường thử nghiệm trước khi tuyên bố các chức năng đó hoạt động.

## Quản trị

Đã đăng nhập admin bằng tài khoản người dùng cung cấp và quan sát dashboard. Dashboard có tám thẻ: đơn hôm nay, lượt truy cập hôm nay, tổng lượt truy cập, đơn chờ xác nhận, doanh thu, số đơn, khách hàng và kho sản phẩm. Con số là dữ liệu thời điểm khảo sát.

Các đường dẫn sau đã được mở trang danh sách, trừ những mục được ghi rõ khác. Mở danh sách **không đồng nghĩa đã thử tạo/sửa/xóa hoặc chứng minh đồng bộ frontend**.

| Nhóm | Các mục quan sát |
| --- | --- |
| Bán hàng | [Sản phẩm](/hg-digital/collections/products), [Biến thể](/hg-digital/collections/product-variants), [Loại biến thể](/hg-digital/collections/variant-types), [Giá trị biến thể](/hg-digital/collections/variant-values), [Danh mục](/hg-digital/collections/categories), [Đơn hàng](/hg-digital/collections/orders), [Chi tiết đơn hàng](/hg-digital/collections/order-items), [Mã giảm giá](/hg-digital/collections/coupons), [Media](/hg-digital/collections/media), [Khách hàng](/hg-digital/collections/customers). |
| Hệ thống | [Người dùng](/hg-digital/collections/users), [Vai trò & Phân quyền](/hg-digital/collections/roles), [Sao lưu & Khôi phục](/hg-digital/collections/backups). Cài đặt Website thấy trên menu tại `/hg-digital/globals/site-settings`, chưa rà toàn bộ form. |
| Nội dung | [Bảng nội dung](/hg-digital/collections/contents), [Danh mục nội dung](/hg-digital/collections/content-categories), [Loại nội dung](/hg-digital/collections/content-types). |
| Giao diện | [Giao diện & SEO các trang](/hg-digital/collections/pages). |

### Biểu mẫu đã xem

- **Sản phẩm**: tab Thông tin chung, Biến thể sản phẩm, Mô tả & Bài viết, Thông số kỹ thuật, Tối ưu SEO. Thông tin chung có tên, ảnh chính, gallery, giá, tồn kho, slug, danh mục, trạng thái kinh doanh. Danh sách có tìm kiếm, hiển thị cột, lọc, các tab trạng thái và phân trang.
- **Mã giảm giá**: mã, tên, hình thức và giá trị giảm, mức tối đa, đơn tối thiểu, giới hạn lượt dùng, trạng thái, thời gian hiệu lực và phạm vi. Chưa xác nhận coupon được áp dụng trong giỏ hay checkout.
- **Vai trò**: tên, slug, mô tả, super admin, bảng quyền theo collection gồm xem/thêm/sửa/xóa và dữ liệu JSON. Chưa thử gán quyền hoặc xác minh quyền được thực thi.
- **Danh mục nội dung**: tên, ảnh, mô tả, slug, loại nội dung, danh mục cha, thứ tự, nhóm SEO gồm title, mô tả, ảnh chia sẻ và xem trước kết quả tìm kiếm.
- **Loại nội dung**: tên, mô tả, biểu tượng, slug, thứ tự và trạng thái hoạt động.
- **Giao diện trang**: tên trang, loại/bố cục, URL, tab cấu hình giao diện và tab SEO/chia sẻ. Chưa xác minh lưu thay đổi có tác động tới trang khách.

## Các quan hệ cần xác minh khi triển khai

Các quan hệ sau được suy ra từ tên và vị trí màn hình, **không phải kết luận đã thử**: sản phẩm/danh mục/biến thể admin → danh sách và chi tiết trang khách; coupon admin → giỏ/checkout; đơn khách → danh sách đơn/chi tiết đơn admin; nội dung và cấu hình SEO → các trang khách. Cần kiểm thử tạo/sửa trong môi trường an toàn, kiểm tra quyền và kết quả thực tế trước khi kết luận.

## Công việc còn thiếu

1. Tiếp tục bấm kiểm tra từng tab, tìm kiếm, trạng thái rỗng/lỗi, responsive và điều hướng của từng trang khách và từng collection admin.
2. Kiểm thử các form tạo/sửa/xóa, media, backup/restore và ma trận quyền với dữ liệu thử và quyền phù hợp; ghi lại kết quả và dọn dữ liệu thử nếu được phép.
3. Kiểm thử quy trình mua hàng hoàn chỉnh trong môi trường thử, bao gồm coupon, các phương thức thanh toán, xác nhận đơn, phiếu có thể in, cập nhật admin và các trạng thái lỗi. Không thực hiện thanh toán/đặt hàng thật chỉ để khảo sát.
4. So sánh từng chức năng với bản Laravel local và ghi rõ đã có, thiếu, khác biệt; tài liệu này không khẳng định bản local đã clone đầy đủ.
