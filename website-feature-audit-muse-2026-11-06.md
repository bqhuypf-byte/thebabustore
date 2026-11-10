# Trang sản phẩm theo mẫu Muse — bàn giao ngày 06/11/2026

## Phạm vi và trạng thái

Đã sửa repo Laravel hiện có tại G:\TeraBoxDownload\thebabustore.vn - Copy và database MySQL local. Chưa commit, push hoặc triển khai lên máy chủ production. Tài liệu này thay phần quy chuẩn Geist ở các bản bàn giao trước: font trang chi tiết sản phẩm hiện là Roboto theo Muse.

Nguồn đối chiếu: [artifact Muse của người dùng](https://muse.ai/library/artifacts/trang-san-pham-kz-zsn-cai-tien). Đã đọc nội dung, CSS và tương tác trong iframe bằng phiên đăng nhập sẵn có. Dữ liệu mẫu được lưu tại outputs/du-lieu-tham-chieu-muse-nut75.json trong workspace Codex.

## Bằng chứng và phạm vi kiểm tra

| Hạng mục | Bằng chứng |
| --- | --- |
| Muse | Đọc trực tiếp bố cục, kiểu chữ, bộ chọn phiên bản/màu, bốn tab, bảo hành và trạng thái chưa có đánh giá |
| KZ ZSN local | Trình duyệt desktop 1440×1000; chọn Pro → Pro X đổi giá 369.000₫ → 409.000₫, ảnh, SKU và thông tin driver/trở kháng |
| NUT75 local | Trình duyệt 390×844; nội dung, ảnh, bảng thông số, thanh mua hàng; chiều rộng tài liệu 390px, không tràn ngang |
| Tab bảo hành | Hiển thị đúng 12 tháng cho KZ ZSN sau xác nhận của người dùng |
| Tab đánh giá | Mở được form, có 5 nút chọn sao, họ tên, nhận xét; không gửi đánh giá thật |
| Admin | Kiểm thử Filament thực thi lưu nội dung; khách hàng bị từ chối sửa; không đăng nhập admin bằng tài khoản tạo mới trong trình duyệt |
| Danh mục | Đối soát database toàn bộ 134 sản phẩm; kiểm tra giao diện trực tiếp trên mẫu đại diện, không tuyên bố đã xem thủ công cả 134 trang |
| Mua hàng | Kiểm thử giỏ hàng, biến thể hết hàng, checkout và quyền xem biên nhận trong transaction rollback; không đặt đơn thực tế |

Trang kiểm tra:

- [KZ ZSN](http://127.0.0.1:8000/product/tai-nghe-nhet-tai-in-ear-kz-zsn-series-pro-pro-x-pro-2-hifi-driver-hybrid-co-mic)
- [Weikav NUT75](http://127.0.0.1:8000/product/weikav-nut75)
- [Sitemap sản phẩm](http://127.0.0.1:8000/sitemap-products.xml)
- [Admin](http://127.0.0.1:8000/hg-digital)

## Quy cách giao diện dùng chung

Roboto được tải từ các file font sẵn có, có bộ ký tự tiếng Việt. Nội dung từ trình soạn thảo bị loại định dạng font/cỡ chữ dán vào; vẫn giữ ảnh, liên kết, danh sách và bảng.

| Thành phần | Desktop | Mobile |
| --- | --- | --- |
| Tên sản phẩm | 27px, đậm 800, dòng 34,56px | 23px |
| Nội dung | 15px, dòng 23,25px | 15px |
| Tiêu đề mục | 20px, dòng 27px | 20px |
| Tiêu đề nhỏ | 16px, đậm 700 | 16px |
| Bảng thông số | 14,5px | 14,5px |
| Nhãn tab | 14px, đậm 600 | 14px |
| Giá chính | 34px, đỏ #d92d20 | 29px |

Khung tối đa 1200px, hai cột desktop, một cột dưới 1020px. Màu xanh #182abb, nền xanh nhạt #eef1fe, đường viền #e4e7ec. Ảnh chính, dải thumbnail, bảng giá, các bước chọn phân loại, thông tin nổi bật, số lượng, hai nút mua, bốn tab và sản phẩm liên quan dùng chung template.

Từ 640px trở xuống có thanh mua cố định. Khoảng cuộn tab đã tính chiều cao header; nút dịch không che thanh mua. Header/footer vẫn kết nối với chức năng cửa hàng hiện có. Ảnh, giá, tồn kho và thông số dùng dữ liệu sản phẩm thật; không sao chép số giá hoặc thông số mẫu Muse sang sản phẩm khác.

## Nội dung đã lưu

- 134/134 sản phẩm đã chuẩn hóa.
- 339 mục nội dung có thể sửa riêng trong admin.
- 425 dòng thông số ở 78 sản phẩm có thông tin rõ dạng nhãn–giá trị; phần còn lại vẫn có mô tả đã chuẩn hóa và bảng thông tin sản phẩm/biến thể.
- Giữ đủ 346 ảnh trong nội dung.
- 134 sản phẩm có tiêu đề SEO và mô tả SEO.
- Nội dung đang hiển thị dùng bản có cấu trúc. Trường mô tả gốc được giữ để đối chiếu; không phải nội dung bỏ dở chờ admin tự sửa.
- Đã xử lý trường hợp cả bài/ảnh nằm trong thẻ heading, HTML có dấu nháy bị escape, chữ định dạng quá lớn và mô tả ngắn chỉ có dữ liệu thử.
- KZ ZSN (ID 663) và Mchose K99 (ID 628): tất cả biến thể đã đổi thành 365 ngày và hiển thị 12 tháng, theo xác nhận của người dùng. Câu thời hạn trong nội dung KZ cũng đã cập nhật.

Thông tin không có trong nguồn không được tự bịa. Các con số kỹ thuật được giữ theo dữ liệu của cửa hàng; đây không phải đợt xác minh thông số của toàn bộ danh mục với nhà sản xuất. Thời hạn bảo hành hãng và thời hạn cửa hàng của các sản phẩm khác được giữ theo nguồn.

## Luồng tương tác

1. Mở sản phẩm → chọn biến thể còn hàng mặc định → ảnh, giá, SKU và thông tin liên quan được cập nhật.
2. Chọn nhóm đầu rồi nhóm sau → chỉ các tổ hợp thực sự có trong database được mua; thay nhóm sau không tự đổi lựa chọn ở nhóm trước. Khi thay nhóm trước cần đổi tổ hợp, có thông báo chọn phiên bản phù hợp.
3. Xem thông số → chuyển tab và cuộn tới bảng; bảng chung và thông số từng dòng máy vẫn giữ ngữ cảnh riêng.
4. Bấm thêm giỏ/mua ngay → kiểm tra lại tồn kho từ database trước khi xử lý.
5. Mở Đánh giá → Viết đánh giá → chọn sao, nhập tên và nhận xét. Luồng gửi có validation và trạng thái chờ duyệt; không tạo đánh giá hay điểm sao giả.

## Quản trị

Trong Sản phẩm → sửa sản phẩm:

- **Mô Tả Chi Tiết**: thêm, bớt, ẩn và sắp xếp mục; sửa văn bản, HTML/ảnh, chú thích ảnh.
- **Thông Số Kỹ Thuật**: thêm nhóm, thêm dòng nhãn/giá trị và sắp xếp.
- **SEO & Bảo Hành**: sửa tiêu đề SEO, mô tả SEO, điều kiện bảo hành riêng.
- **Biến Thể Sản Phẩm**: sửa SKU, giá, tồn kho, ảnh, thời hạn bảo hành, cặp tên nhóm/giá trị lựa chọn và thông số riêng.
- **Thứ tự nhóm lựa chọn**: quản trị trình tự khách chọn; không giới hạn ba nhóm.

12 tháng được lưu là 365 tại trường thời hạn bảo hành (ngày). Nội dung điều kiện riêng không thay đổi trường thời hạn của biến thể. Đánh giá chỉ hiển thị khi đã duyệt theo cơ chế hiện có.

Quyền CRUD sản phẩm/biến thể dựa trên quyền admin hiện có. Sản phẩm/biến thể có lịch sử đơn hàng không bị xóa trực tiếp; có thể ngừng bán. Khi xóa toàn bộ mục đã chuẩn hóa, nội dung cũ không tự xuất hiện lại.

## SEO và kiểm thử

Đã có title/description riêng, canonical, Open Graph/Twitter, một H1 trên trang, nội dung các tab có sẵn trong HTML, alt ảnh, ảnh nội dung tải lazy, sitemap sản phẩm đang hoạt động. URL cũ /product1 chuyển 301 về /product. Query variant chọn đúng phiên bản và giữ canonical sản phẩm.

JSON-LD dùng ProductGroup/hasVariant cho nhiều biến thể, Product cho sản phẩm đơn, Offer với giá/tồn kho thực tế và BreadcrumbList. Chỉ thêm aggregateRating khi có đánh giá đã duyệt. Thiết kế bám [hướng dẫn biến thể sản phẩm của Google](https://developers.google.com/search/docs/appearance/structured-data/product-variants) và [dữ liệu sản phẩm của Google](https://developers.google.com/search/docs/appearance/structured-data/product-snippet). Chưa xác minh rich results trên URL production hoặc gửi sitemap lên Search Console.

19 bài kiểm thử qua, 128 assertions: ProductMuseCatalogTest, ProductContentManagementTest, ProductDetailExperienceTest, CheckoutOrderTest, OrderReceiptTest. Sau sửa JS layout, chạy lại 5 bài liên quan hiển thị/SEO: 34 assertions qua. Diff của các file liên quan sạch; toàn repo còn một số cảnh báo dòng trống cuối file ở thay đổi cũ ngoài phạm vi. Console trang NUT75 không còn lỗi sau sửa loadingScreen và tracking trên localhost.

## Sao lưu và vận hành

Trong workspace Codex: work/muse-clone/catalog-before-20261106.json lưu trường sản phẩm trước chuẩn hóa; catalog-before-refinement-20261106.json lưu sản phẩm và biến thể trước tinh chỉnh/bảo hành. Bản file trước sửa nằm trong work/muse-clone/before và work/product-standard/before. Không chứa tài khoản đăng nhập trong tài liệu này.

Migration mới: 2026_11_06_000002_add_structured_product_content và 2026_11_06_000003_add_product_seo_and_content_revision. Lệnh catalog:standardize mặc định chỉ xem trước; --apply yêu cầu đường dẫn backup mới và bỏ qua sản phẩm đã chuẩn hóa. Các lần sửa admin sau đó được giữ.

Khi triển khai production sau này cần áp dụng code/migration và dữ liệu đã duyệt, kiểm tra địa chỉ APP_URL, ảnh storage và chạy xác nhận trên domain thật. Việc cập nhật local hiện tại không tự thay đổi website production.
