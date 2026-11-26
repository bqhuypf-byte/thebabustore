# Bộ ảnh trang chủ — 26/11/2026

Tạo bằng công cụ imagegen tích hợp, tham chiếu ảnh sản phẩm trong thư viện shop. Chỉ cập nhật ảnh trang chủ, ảnh danh mục và ảnh bìa blog; không sửa dữ liệu sản phẩm, thông số, giá, biến thể hoặc kho.

## Bố trí

- Carousel: WEIKAV MAX102 (tính năng), ATK A9 (tính năng), AULA F108 Pro (Black Friday), MCHOSE V9 Pro (Black Friday). Từng banner liên kết tới đúng trang sản phẩm tại local.
- Desktop: 2160 × 720 px, tỉ lệ 3:1. Điện thoại: bản bố cục riêng 1200 × 800 px, tỉ lệ 3:2.
- Bốn danh mục: 1200 × 600 px.
- Sáu ảnh bìa blog: 1600 × 900 px, tỉ lệ 16:9.
- Góc máy: 2400 × 800 px. BABU HUB và đăng ký email: 1200 × 600 px.
- WebP chất lượng 86 cho ảnh đang dùng; PNG gốc giữ tại `storage/app/public/homepage/2026-11/originals`.
- Toàn bộ ảnh WebP: `storage/app/public/homepage/2026-11`. Prompt và nguồn ảnh tạo: `docs/homepage-media/generated-assets.json`.

## Nguồn đối chiếu chữ trên banner

Chỉ sử dụng các thông số khớp giữa dữ liệu shop và tài liệu hãng. Không lấy giá, phần trăm giảm, quà tặng hoặc thời hạn từ chương trình của hãng để áp cho shop.

- [WEIKAV MAX102](https://weikav.com/product/weikav-max102/): 102 phím, vỏ nhôm 6063, USB-C / 2.4 GHz / Bluetooth. Không đưa số liệu hiệu năng ước tính.
- [ATK A9 Series](https://www.atk.store/products/atk-dragonfly-a9-series-lightweight-wireless-mouse): thiết kế đối xứng. Không dùng cảm biến, trọng lượng hoặc polling rate của bản Ultra để quảng cáo chung cho mọi phiên bản.
- [AULA F108 Pro](https://www.aulagaming.com/pages/aula-f108-pro-details): full-size, màn hình TFT, núm xoay. Chỉ quảng cáo dòng Pro có màn hình/núm xoay, không áp cho F108 thường.
- [MCHOSE V9 Series](https://www.mchose.store/products/mchose-v9-series-wireless-gaming-headset): V9 Pro driver 53 mm, kết nối có dây / Bluetooth / 2.4 GHz. Không dùng số giờ pin chung cho V9 và V9 Pro.

Ảnh là minh họa chiến dịch dựa trên tham chiếu sản phẩm; không dùng làm sơ đồ layout hay sơ đồ phần cứng. Ảnh sản phẩm gốc và mô tả gốc được giữ nguyên.

## Sửa trong admin

- Slide: thay ảnh desktop; bản mobile của bộ ảnh này tự lấy từ tên sibling `-mobile.webp` khi tồn tại. Khi upload ảnh khác, nếu không có bản mobile tương ứng thì dùng ảnh đã upload và giữ trọn ảnh.
- Danh mục: sửa ảnh Banner.
- Blog: sửa ảnh bìa của bài tương ứng.
- Cài đặt > Trang chủ: sửa Ảnh chính của khối catalog, BABU HUB và khối Đăng ký email: ảnh nền.
- Chữ trong ảnh sửa bằng thiết kế lại từ PNG/prompt; chữ góc máy, HUB và form email vẫn là nội dung HTML hiện có.

Bản sao dữ liệu ảnh trước khi thay lưu tại `storage/app/backups/homepage-media-*.json`; đường dẫn chính xác có trong báo cáo áp dụng. Không gọi image update observers vì chúng xóa file cũ; bản cập nhật có kiểm soát chỉ thay đường dẫn và giữ lại file gốc.
## Kiểm tra hoàn tất

- 21 ảnh WebP được xuất, tổng 1.770.724 byte (~1,69 MiB); PNG gốc và prompt giữ trong workspace.
- Trình duyệt desktop 1440 px: banner 3:1, không tràn ngang; bốn liên kết slide dùng route `/products/{slug}` thực tế.
- Trình duyệt mobile 390 px: banner 3:2, tải đúng ảnh `-mobile.webp`; không tràn ngang. 13 ảnh banner/blog/cộng đồng đều tải thành công.
- Kiểm tra trực quan banner chính, danh mục, góc máy, blog và form email.
- `php artisan view:cache`: thành công.
- `HomeNewsletterTest`: 2 tests, 11 assertions, thành công.
- Sao lưu dữ liệu trước thay: `storage/app/backups/homepage-media-20261126-105624.json`.
- Bốn model được kiểm tra lại có biến thể `in_stock` ngay trước khi áp dụng. Không cập nhật bảng products hoặc product_variants.
