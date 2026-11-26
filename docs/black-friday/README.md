# Black Friday — quản lý chiến dịch riêng

Ngày triển khai: 26/11/2026. Bản local: http://127.0.0.1:8000/black-friday

## Quản lý trong admin

Vào **Quản lý bán hàng → Chiến dịch khuyến mãi → Black Friday**.

- Danh sách: `/hg-digital/promotion-campaigns`
- Chiến dịch đã tạo: `/hg-digital/promotion-campaigns/1/edit`
- Quản lý tên, đường dẫn, tiêu đề, giới thiệu, điều kiện thực tế.
- Ba ô ảnh riêng: banner nhỏ trên trang sản phẩm, banner trang sale desktop và banner mobile.
- Bật/tắt trang, bật/tắt banner sản phẩm, lịch bắt đầu/kết thúc, độ ưu tiên banner. Thời gian admin và thời gian hiển thị dùng GMT+7 (Asia/Bangkok).
- Danh sách sản phẩm của mỗi chiến dịch quản lý độc lập: thêm, bỏ, kéo sắp xếp, nhãn nhỏ, ẩn/hiện từng sản phẩm.
- Sản phẩm hết hàng, số lượng bằng 0 hoặc bị ẩn sẽ không xuất hiện ở trang chiến dịch; danh sách cấu hình vẫn được giữ lại.
- Giá trên chiến dịch lấy từ biến thể còn bán được. Nhiều biến thể hiển thị “Từ” với giá thấp nhất. Giá giỏ hàng/thanh toán vẫn được kiểm tra theo biến thể thực tế. Không có lớp giá khuyến mãi ghi đè riêng; sửa giá thật trong mục Sản phẩm/biến thể khi muốn đổi giá bán.

Black Friday được khởi tạo bằng 4 sản phẩm hiện có đang đánh dấu `on_sale` và có biến thể `in_stock`. Đây là thao tác nhập danh sách một lần. Sau đó danh sách chiến dịch không tự đồng bộ theo cờ `on_sale`; admin quản lý riêng.

## Hiển thị

- Banner nhỏ trước vùng chọn phân loại của trang sản phẩm lấy từ chiến dịch đang hoạt động, thay cho banner Slide không liên quan trước đây.
- Bấm banner dẫn tới trang của đúng chiến dịch.
- Khi nhiều chiến dịch cùng bật, ưu tiên số `priority` cao nhất, sau đó ID mới nhất. Không còn chiến dịch phù hợp thì ẩn banner nhỏ.
- Chiến dịch chưa đến lịch, hết lịch hoặc bị tắt trả về 404 và không cung cấp banner.
- `/black-friday` mở chiến dịch slug `black-friday`; các chiến dịch khác mở ở `/khuyen-mai/{slug}`.
- Trang có lọc danh mục, phân trang, xem nhanh, chọn phiên bản và luồng mua hàng hiện có. Không gửi đơn hàng thật trong quá trình kiểm tra.

## Ảnh

Tạo bằng công cụ **imagegen tích hợp**, không dùng API/CLI. Banner chỉ mang chủ đề Black Friday, không tự gán phần trăm giảm, mức giá, quà tặng hay thời hạn chưa được cấu hình.

Ảnh đã gắn tại `storage/app/public/promotions/black-friday-2026/`:

- `product-banner.webp`: 1440 × 480 px.
- `banner.webp`: 2160 × 720 px.
- `banner-mobile.webp`: 1200 × 800 px.
- PNG gốc: `originals/wide.png`, `originals/mobile.png`.
- Prompt đầy đủ: `docs/black-friday/image-prompts.json`.

## Dữ liệu và kiểm tra

Hai bảng mới: `promotion_campaigns`, `promotion_campaign_items`. Không sửa giá, mô tả, ảnh, SKU, tồn kho, bảo hành hay cờ sale của sản phẩm. Báo cáo khởi tạo có kiểm tra hash toàn bộ bảng products và product_variants trước/sau: `install-report.json`.

Migration: `database/migrations/2026_11_26_000001_create_promotion_campaigns.php`. Khi mang source sang môi trường khác cần chạy migration và cấu hình chiến dịch trong admin, đồng thời sao chép ảnh public storage theo quy trình triển khai hiện có.

Kiểm tra hoàn tất:

- 36 tests / 212 assertions đạt: `PromotionCampaignTest`, `ProductDetailExperienceTest`, `PurchaseValidationTest`.
- Admin: thêm/xóa sản phẩm riêng, cập nhật nội dung, bật/tắt, lịch kết thúc không cần lịch bắt đầu, từ chối kết thúc trước bắt đầu, chặn tài khoản khách.
- Trình duyệt desktop 1440 px: banner sản phẩm → `/black-friday`, ảnh tải đúng, bộ lọc bàn phím còn 1 sản phẩm, xem nhanh hoạt động.
- Mobile 390 px: ảnh riêng 3:2, không tràn ngang; popup chọn phiên bản nằm trong màn hình và cuộn được.
- Trang sản phẩm vẫn dùng template và kiểu thẻ hiện có; không áp lại đợt thiết kế toàn website đã được yêu cầu hoàn tác.

## Cập nhật phong cách Black Friday chrome / tím BABU

- Trang chiến dịch dùng nền đen tím, texture và ánh sáng tím, tiêu đề bạc chrome theo ảnh tham chiếu; không sao chép các con số giảm giá, mức giá hay thời gian trong ảnh mẫu.
- Trang chủ có khối `bf-home-event` cùng phong cách, thay khu sale đỏ/vàng khi chiến dịch Black Friday đang hoạt động.
- Banner, đường dẫn và tối đa 8 sản phẩm trong khối trang chủ lấy trực tiếp từ chiến dịch trong admin. Thêm/bỏ, thứ tự, ẩn/hiện và hết lịch áp dụng chung cho cả trang chủ và trang chiến dịch. Khi chiến dịch tắt, trang chủ quay về khối ưu đãi thông thường.
- Bộ lọc danh mục tại trang chủ dùng Alpine; xem nhanh tiếp tục dùng popup homepage hiện có. Trang sale dùng bộ lọc Livewire và popup xem nhanh của danh sách.
- Giá “chỉ từ” được tính từ biến thể đang bán được trong toàn bộ danh sách chiến dịch, không ghi cứng. Các khu deal trên trang hiện tại phân theo giá biến thể còn hàng: dưới 500.000₫ và từ 500.000₫.
- Hệ thống giá, kho, thông số, ảnh sản phẩm và các chức năng đặt hàng giữ nguyên.

Ảnh chrome tạo bằng **imagegen tích hợp** được lưu tại `storage/app/public/promotions/black-friday-2026/chrome/`: desktop 2160×720, mobile 1200×800, banner sản phẩm 1440×480, PNG gốc tại `originals/`. Bộ ảnh trước vẫn được giữ. Prompt và nguồn đầu ra: `docs/black-friday/chrome-image-prompts.json`.

Backup cài đặt banner trước cập nhật: `storage/app/backups/black-friday-chrome-20261126-120835.json`.

Kiểm tra đợt cập nhật: 34 tests / 193 assertions đạt (`PromotionCampaignTest`, `HomepageLayoutTest`, `HomeProductCarouselTest`, `HomeNewsletterTest`, `PurchaseValidationTest`). Đã kiểm tra trình duyệt desktop 1440px và mobile 390px: ảnh đúng tỉ lệ, không tràn ngang, bộ lọc chuột chỉ còn 1 sản phẩm, popup mở đúng chuột ATK X1, liên kết chiến dịch đúng `/black-friday`.
