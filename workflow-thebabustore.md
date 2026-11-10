# Workflow clone website The BABU Store — File làm việc cho Gemini

> File này là file duy nhất cần gửi cho Gemini trong Antigravity. Gemini đọc toàn bộ file này trước, sau đó chỉ thực hiện đúng task được chỉ định trong mỗi lần chạy.

## Cách dùng file này

1. Dán toàn bộ nội dung file này vào Gemini trong Antigravity, hoặc đính kèm file `.md` này vào project.
2. Mỗi lần chỉ ra lệnh làm **1 task**, ví dụ: "Làm Task 1A theo file workflow, xong dừng lại báo cáo".
3. Gemini làm xong task nào phải dừng, liệt kê file đã tạo/sửa, cách chạy thử và kết quả kiểm tra của task đó. Không tự nhảy sang task tiếp theo.
4. Người dùng kiểm tra trên máy local, đạt mới ra lệnh làm task tiếp theo.
5. Nếu thông tin trong file này chưa đủ cho một chi tiết, Gemini phải ghi rõ "chưa có dữ kiện" và hỏi, không tự bịa số liệu, giá, sản phẩm hay chính sách.

Web mẫu cần clone chức năng và giao diện: https://nextjs.hgdigital.vn/

Web đích: **The BABU Store** — shop bán gaming gear, bàn phím cơ và phụ kiện, tên miền `thebabustore.vn`. Mọi sản phẩm, thương hiệu và nội dung trong bản clone phải là của The BABU Store, không dùng dữ liệu của shop khác.

Ngữ cảnh: người dùng đang có một bản web chạy local bằng Laravel tại `http://127.0.0.1:8000/` (ví dụ trang `/products/ban-phim-he-atk-rs6-aluminum`). Địa chỉ local này chỉ mở được trên máy của người dùng. Workflow này dựng bản Next.js mới theo web mẫu, không yêu cầu truy cập vào địa chỉ local đó.

## Quy tắc làm việc bắt buộc

### Thứ tự và phạm vi

- Làm đúng thứ tự Task 1A → 1H, rồi mới tới Task 2, 3, 4, 5. Task 6 và Task 7 là giai đoạn sau, chỉ làm khi người dùng ra lệnh riêng.
- **Phần Admin không thuộc Task 1.** Không dựng trang quản trị, dashboard quản lý hay phân quyền admin trong Task 1. Admin để riêng ở Task 6.
- Chỉ clone **chức năng, luồng hoạt động, bố cục và cảm giác tương tác** của web mẫu. Không sao chép nguyên văn bài viết, mô tả sản phẩm hay hình ảnh của web mẫu để dùng làm dữ liệu thật.
- Dữ liệu mẫu dùng trong lúc dựng (seed data) phải gắn nhãn rõ là dữ liệu mẫu, lấy theo đúng ngành hàng gaming gear. Khi có danh sách sản phẩm thật, thay toàn bộ dữ liệu mẫu bằng Task 2.

### Kỹ thuật

- Frontend và backend phía khách hàng dùng **Next.js** (App Router) + TypeScript + Tailwind CSS. Icon dùng bộ **Lucide** (`lucide-react`). Animation dùng CSS transition của Tailwind, chỉ thêm thư viện animation khi một hiệu ứng trong đặc tả không làm được bằng CSS.
- Giỏ hàng phải hoạt động nhất quán giữa các tab và sau khi tải lại trang. Không để state cũ ghi đè state mới.
- Mọi trang phải responsive: desktop, tablet và mobile đều dùng được. Ô tìm kiếm không được biến mất ở màn hình hẹp mà không có nút mở lại.
- Giá tiền hiển thị theo định dạng Việt Nam, ví dụ `5.500.000₫`. Đơn vị tiền lưu trong database là số nguyên VND.
- SEO cơ bản cho từng trang: `title`, meta description, canonical, Open Graph, sitemap và robots. Dùng component ảnh của Next.js (`next/image`) cho ảnh sản phẩm.

### Làm việc minh bạch — phải show màn hình, không làm ngầm

- Khi thực hiện bất kỳ task clone nào (frontend, backend, đối chiếu web mẫu, kiểm tra kết quả), Gemini phải **làm việc trực tiếp trên màn hình mà người dùng nhìn thấy được** trong Antigravity: mở trình duyệt, mở file, mở terminal ở chế độ hiển thị, để người dùng theo dõi được từng bước đang làm. Không chạy ngầm, không làm hàng loạt thao tác khuất rồi chỉ báo kết quả cuối cùng.
- Khi cần đối chiếu web mẫu, Gemini phải mở trực tiếp https://nextjs.hgdigital.vn/ trong trình duyệt hiển thị, thao tác từng bước trên trang đó (mở trang chủ, trang cửa hàng, chi tiết sản phẩm, xem nhanh, giỏ hàng, thanh toán...) và vừa thao tác vừa giải thích đang xem gì, lấy thông tin gì để áp dụng vào bản clone.
- Khi code và kiểm tra bản clone, Gemini phải mở trực tiếp trang local đang dựng trên trình duyệt hiển thị, bấm thử từng chức năng vừa làm (tìm kiếm, lọc, xem nhanh, thêm giỏ, voucher, thanh toán...) để người dùng nhìn thấy kết quả thật, không chỉ nói "đã xong" dựa trên code.
- Trước mỗi bước lớn phải nói rõ sắp làm gì; làm xong bước đó phải dừng mô tả ngắn gọn đã thấy/đã làm được gì rồi mới đi tiếp. Nếu một thao tác buộc phải chạy nền (ví dụ cài thư viện, build), phải báo trước lệnh sẽ chạy, lý do, và show log/kết quả ngay khi xong.
- Người dùng có quyền ngắt, yêu cầu dừng hoặc làm lại ở bất kỳ bước nào đang hiển thị. Gemini phải dừng ngay khi được yêu cầu, không tiếp tục chạy ngầm sau đó.
- Vi phạm quy tắc này — làm xong cả task mà người dùng không nhìn thấy quá trình — coi như task chưa đạt nghiệm thu, phải làm lại phần kiểm tra và trình diễn trên màn hình.

### Báo cáo sau mỗi task

Mỗi task kết thúc phải có đủ 4 mục:

1. Đã làm gì, tạo/sửa những file nào.
2. Cách chạy thử trên local.
3. Từng mục trong checklist nghiệm thu của task: đạt / chưa đạt, kèm bằng chứng (ảnh chụp màn hình hoặc kết quả chạy thử).
4. Vấn đề còn tồn tại hoặc điểm chưa có dữ kiện.

Thiếu 1 trong 4 mục coi như task chưa xong.

## Kết quả khảo sát trực tiếp web mẫu

Phần này là dữ kiện đã kiểm chứng trên https://nextjs.hgdigital.vn/ ngày 03-11-2026, gồm đọc trang tĩnh và bấm thử từng chức năng. Gemini dùng phần này làm đặc tả, không cần và không được đoán thêm tính năng ngoài danh sách này, trừ các mục ghi rõ "suy luận".

### Bản đồ trang (sitemap phía khách hàng)

| Đường dẫn | Trang | Ghi chú |
| --- | --- | --- |
| `/` | Trang chủ | Danh mục, sản phẩm bán chạy, xem nhanh |
| `/products` | Cửa hàng | 22 sản phẩm, tìm, lọc, sắp xếp |
| `/products/[slug]` | Chi tiết sản phẩm | Biến thể, SKU, tab thông tin, đánh giá |
| `/cart` | Giỏ hàng | Voucher, thanh tiến trình freeship |
| `/checkout` | Thanh toán | VietQR, COD, MoMo |
| `/orders` | Tra cứu đơn hàng | Tìm theo mã đơn, chip trạng thái |
| `/login` | Đăng nhập / Đăng ký | 2 tab trong cùng một trang |
| `/guides` | Cẩm nang & hướng dẫn | Tìm kiếm và lọc bài viết |
| `/guides/[slug]` | Chi tiết bài hướng dẫn | Suy luận theo cấu trúc danh sách bài |

Ví dụ slug sản phẩm đã kiểm chứng: `/products/ghe-cong-thai-hoc-saigu-ares-l8`. Ảnh sản phẩm được phục vụ qua đường dẫn dạng `/_next/image?url=/api/media/file/...` — tức backend có endpoint media riêng, xem mục Backend.

### Trang chủ

- Khối danh mục "Khám Phá Theo Nhu Cầu Gaming Của Bạn" gồm các thẻ danh mục, mỗi thẻ có nút "Xem ngay" dẫn tới `/products?category=[slug]`. Các nhóm đã thấy: Bàn Phím Cơ, Chuột Gaming, Switch & Lube, Keycap & Phụ Kiện, Tai Nghe & IEM.
- Khối "Sản Phẩm Đang Được Game Thủ Yêu Thích" (Top Bán Chạy Nhất) hiển thị thẻ sản phẩm và link "Xem tất cả (22 sản phẩm)" tới trang cửa hàng.
- Thẻ sản phẩm gồm: ảnh, tên, giá hiện tại, giá cũ gạch ngang và nhãn giảm giá dạng `-8%` khi có khuyến mãi.
- Trên thẻ sản phẩm có nút thêm vào giỏ nhanh và nút "Xem nhanh" hiện khi di chuột (hover) lên thẻ.
- Bấm thêm vào giỏ: hiện thông báo toast "Đã thêm vào giỏ hàng!" và badge số lượng trên icon giỏ ở header cập nhật ngay.

### Trang cửa hàng `/products`

- Tiêu đề khu vực "Cửa Hàng Gaming Gear & Phím Cơ", kèm dòng đếm "Hiển thị 22 sản phẩm".
- Tìm kiếm: gõ từ khóa và Enter sẽ lọc danh sách, URL cập nhật tham số `?q=`.
- Chip danh mục: bấm "Chuột gaming" chuyển URL thành `?category=chuot-gaming` và chỉ hiện sản phẩm thuộc danh mục đó.
- Lọc khoảng giá ở sidebar, đã kiểm chứng các khoảng: "Dưới 500.000₫", "1.000.000₫ – 2.000.000₫". Lọc cho kết quả đúng khoảng giá.
- Sắp xếp có 4 tùy chọn: Nổi bật nhất, Đánh giá cao nhất, Giá tăng dần (`price-asc`), Giá giảm dần (`price-desc`). Đã kiểm chứng thứ tự giá tăng dần và giảm dần là đúng.
- Nút "Đặt lại bộ lọc" xóa danh mục và khoảng giá đã chọn, giữ nguyên kiểu sắp xếp.

### Chi tiết sản phẩm `/products/[slug]`

- Breadcrumb: Trang chủ / Sản phẩm / Danh mục / Tên sản phẩm.
- Gallery ảnh lớn kèm thumbnail, chú thích ảnh chụp tại showroom.
- Thông tin chính: nhãn danh mục, trạng thái tồn kho dạng "Còn 25 sản phẩm", tên sản phẩm, điểm đánh giá dạng "5.0 (15 đánh giá)", giá mới và giá cũ, mã SKU.
- Chọn biến thể sẽ đổi SKU tương ứng. Ví dụ đã kiểm chứng: SKU đổi từ `K87S-WHT-PUR` sang `K87S-BLK-GRY` khi đổi biến thể.
- Khối "Đặc điểm kỹ thuật chính": chính hãng / nhà phân phối, bảo hành, tình trạng hàng.
- Bộ chọn số lượng tăng/giảm, nút "Thêm Vào Giỏ Hàng" và các badge dịch vụ: "Bảo hành 12 tháng", "Freeship toàn quốc", "Lube mod miễn phí".
- 4 tab nội dung: Mô tả, Thông số, Bảo hành, Đánh giá. Tab Đánh giá có form chọn sao, họ tên, bình luận và nút "Gửi đánh giá".
- Khối "Sản phẩm liên quan" ở cuối trang, kèm link "Xem tất cả".

### Xem nhanh sản phẩm (Quick View)

- Kích hoạt: hover lên thẻ sản phẩm, bấm nút "Xem nhanh" kèm icon con mắt.
- Hành vi: mở **popup/modal ngay tại trang đang đứng**, không chuyển trang.
- Nội dung popup: gallery ảnh + thumbnail, link "Xem trang chi tiết", tên sản phẩm, badge danh mục, đánh giá và tồn kho, giá cũ/mới, chọn biến thể, điểm nổi bật, tăng/giảm số lượng, nút "Thêm Vào Giỏ", nút "Mua Ngay", các badge dịch vụ (Bảo hành 12T, Freeship, Lube mod) và nút X để đóng.

### Giỏ hàng `/cart`

- Tăng/giảm số lượng từng dòng, xóa từng sản phẩm, "Xóa tất cả" — tổng tiền cập nhật đúng sau mỗi thao tác và có toast xác nhận.
- Thanh tiến trình freeship: ngưỡng miễn phí vận chuyển là **500.000₫**. Chưa đủ hiện dạng "Mua thêm 497.000₫ để được Freeship", đủ rồi hiện "đã đủ điều kiện Miễn phí vận chuyển" và thanh đầy. Giỏ trống thì ẩn thanh này.
- Voucher: nhập mã và áp dụng. Mã sai báo lỗi rõ ràng dạng: mã không tồn tại trong hệ thống. Mã đúng `BABU10` đã kiểm chứng được trừ **100.000₫** (mức trần của mã) và hiện toast thành công. Có nút "Bỏ mã" để gỡ voucher.
- Giỏ trống hiện thông báo "Giỏ hàng hiện đang trống" và nút "Khám phá sản phẩm ngay".

### Thanh toán `/checkout`

- Form thông tin giao hàng: họ tên, số điện thoại, email, tỉnh/thành phố (mặc định Hà Nội), địa chỉ, ghi chú.
- 3 phương thức thanh toán: **VietQR** (được chọn mặc định, hiển thị thông tin ngân hàng và mã QR chuyển khoản), **Ship COD**, **Ví MoMo**.
- Ô nhập voucher kèm các nút voucher nhanh đã thấy: `BABU10`, `FREESHIP`, `RAINY75`, `KEYCAP50K`, `VIPGAMER`. Giá trị chi tiết của từng mã ngoài `BABU10` chưa kiểm chứng được — Gemini không tự đoán, phải để cấu hình trong database/seed và ghi rõ mã nào là mẫu.
- Khối tóm tắt đơn hàng và nút "Xác Nhận Đặt Hàng".
- Giỏ trống vào trang này sẽ hiện thông báo giỏ trống và nút quay lại cửa hàng.
- Web mẫu **không có** nút xuất, in hay tải hóa đơn ở phía khách hàng (đã kiểm tra ở giỏ hàng, thanh toán và tra cứu đơn). Không tự thêm tính năng này vào Task 1.

### Tra cứu đơn hàng `/orders`

- Tìm đơn theo mã đơn, lọc trực tiếp khi nhập. Mã không tồn tại hiện "Không tìm thấy đơn hàng nào".
- Có các chip lọc theo trạng thái đơn (số lượng từng trạng thái hiển thị trên chip) và nút "Làm mới".
- Không có nút xuất/in/tải hóa đơn tại trang này trên web mẫu.

### Đăng nhập / Đăng ký `/login`

- Một trang, 2 tab: "Đăng Nhập" và "Tạo Tài Khoản".
- Tab Đăng Nhập: email, mật khẩu, link "Quên mật khẩu?", nút đăng nhập. Web mẫu có tài khoản mẫu để thử — bản clone chỉ tạo tài khoản mẫu trong seed local, không dùng thông tin thật.
- Tab Tạo Tài Khoản: họ tên, email, số điện thoại, mật khẩu.
- Mục đích đăng nhập theo web mẫu: theo dõi đơn hàng, nhận ưu đãi thành viên, bảo hành nhanh.

### Cẩm nang `/guides`

- Tiêu đề "Cẩm Nang Kỹ Thuật & Hướng Dẫn Sử Dụng": bài về kết nối Bluetooth/2.4GHz, chỉnh keymap VIA, màn hình LED, lube switch, cân stab.
- Mỗi thẻ bài gồm: ảnh bìa, nhãn danh mục, tiêu đề, tóm tắt, thời gian đọc dạng "5 phút đọc", link "Đọc ngay".
- Danh mục bài đã thấy: Kiến Thức Cơ Bản, Hướng Dẫn Thiết Bị, Gaming Audio, Modding & Custom, Bàn Phím Văn Phòng.
- Tìm kiếm bài và lọc theo danh mục hoạt động, có thể kết hợp cả hai.
- Nội dung bài trong bản clone phải viết mới hoặc dùng bài của The BABU Store do người dùng cung cấp, không chép nguyên văn bài của web mẫu.

### Component dùng chung

- Header: logo The BABU Store, ô tìm kiếm có dropdown gợi ý sản phẩm (gõ "Rainy75" hiện nhóm "Sản phẩm phù hợp" kèm số kết quả), icon tài khoản, icon giỏ hàng kèm badge số lượng và tổng tiền.
- Tìm kiếm ở header: gõ là hiện dropdown gợi ý ngay, bấm vào gợi ý đi tới sản phẩm.
- Footer: thông tin shop, liên kết danh mục/hỗ trợ, thông tin liên hệ — nội dung lấy từ thông tin thật của The BABU Store do người dùng cung cấp, thiếu thì để placeholder ghi rõ "chờ cung cấp".
- Thẻ sản phẩm dùng chung cho trang chủ, cửa hàng, sản phẩm liên quan và kết quả tìm kiếm.
- Toast thông báo: dùng cho thêm giỏ, xóa giỏ, áp dụng/gỡ voucher, gửi đánh giá.
- Modal Xem nhanh dùng chung ở mọi nơi có thẻ sản phẩm.
- Breadcrumb dùng cho chi tiết sản phẩm, giỏ hàng, thanh toán, guides.

### Mô hình dữ liệu tối thiểu

Gemini dựng database theo các bảng/kiểu dữ liệu sau. Tên trường có thể điều chỉnh theo convention của Next.js nhưng ý nghĩa không được thiếu.

- `Category`: id, name, slug, description, image.
- `Product`: id, name, slug, categoryId, price, compareAtPrice (giá cũ), stock, sku, ratingAvg, ratingCount, highlights, description, specs (JSON), warrantyInfo, isFeatured, images[].
- `ProductVariant`: id, productId, name, sku, price (nếu khác giá gốc), stock. Đổi biến thể phải đổi SKU hiển thị.
- `Review`: id, productId, authorName, rating (1–5), comment, createdAt.
- `CartItem` (lưu phía client và đồng bộ server khi đăng nhập nếu làm): productId, variantId, quantity.
- `Voucher`: code, type (`percent` hoặc `fixed`), value, maxDiscount, minOrderValue, isActive. Seed mẫu: `BABU10` giảm tới trần 100.000₫ đã kiểm chứng; các mã còn lại để `isActive` và giá trị do người dùng xác nhận sau.
- `Order`: id/code, customerName, phone, email, province, address, note, paymentMethod (`vietqr` | `cod` | `momo`), items[], subtotal, discount, shippingFee, total, status, createdAt.
- `User`: id, name, email, phone, passwordHash, createdAt.
- `Guide`: id, title, slug, category, excerpt, content, coverImage, readingMinutes, createdAt.

### Backend phía khách hàng (phạm vi Task 1)

Backend ở đây là backend phục vụ các trang khách hàng ở trên, không gồm Admin.

- API/Server Actions lấy danh sách sản phẩm có `q`, `category`, `priceRange`, `sort`.
- API chi tiết sản phẩm theo slug, kèm biến thể, đánh giá và sản phẩm liên quan.
- API tìm kiếm gợi ý cho header (trả về tối đa vài sản phẩm phù hợp).
- API media phục vụ ảnh sản phẩm/bài viết, tương tự cách web mẫu dùng `/api/media/file/...`, hoặc dùng storage tương đương và ghi rõ lựa chọn.
- API giỏ hàng/đặt hàng: tạo đơn từ giỏ, tính lại giá, voucher và phí ship ở server (không tin số tiền gửi từ client), sinh mã đơn duy nhất.
- API voucher: kiểm tra mã, trả về số tiền giảm và thông báo lỗi khi mã không tồn tại/hết hiệu lực.
- API tra cứu đơn theo mã đơn, trả về trạng thái và danh sách sản phẩm trong đơn.
- API auth: đăng ký, đăng nhập, đăng xuất, quên mật khẩu (gửi link đặt lại qua email khi có cấu hình SMTP; chưa có thì ghi rõ chưa kích hoạt).
- API guides: danh sách, tìm kiếm, lọc danh mục, chi tiết bài.
- Thanh toán VietQR: hiển thị QR/thông tin chuyển khoản từ cấu hình shop, nội dung chuyển khoản chứa mã đơn. COD và MoMo ở Task 1 chỉ ghi nhận phương thức khách chọn, không tự tích hợp cổng thanh toán thật khi chưa có tài khoản/keys do người dùng cung cấp.

### Icon — quy định bắt buộc

Dùng nhất quán bộ Lucide (`lucide-react`) trong toàn site, không trộn nhiều bộ icon và không dùng emoji thay icon.

| Vị trí | Icon Lucide | Hành vi |
| --- | --- | --- |
| Tìm kiếm header/cửa hàng/guides | `Search` | Mở/đặt focus ô tìm, gửi tìm kiếm |
| Giỏ hàng header/thẻ sản phẩm | `ShoppingCart` | Thêm vào giỏ hoặc đi tới `/cart` |
| Xem nhanh trên thẻ sản phẩm | `Eye` | Mở modal Xem nhanh |
| Tài khoản header | `User` | Đi tới `/login` hoặc menu tài khoản |
| Đóng modal/drawer | `X` | Đóng popup |
| Tăng/giảm số lượng | `Minus`, `Plus` | Đổi số lượng |
| Xóa sản phẩm khỏi giỏ | `Trash2` | Xóa dòng giỏ |
| Đánh giá sao | `Star` | Hiển thị điểm và chọn sao khi đánh giá |
| Freeship | `Truck` | Badge/thanh tiến trình freeship |
| Bảo hành | `ShieldCheck` | Badge bảo hành |
| Lube/mod miễn phí | `Wrench` hoặc `Settings` | Badge dịch vụ, chọn một và dùng nhất quán |
| Voucher | `TicketPercent` hoặc `Tag` | Ô nhập và nút voucher, chọn một |
| Thanh toán VietQR/COD/MoMo | `QrCode`, `Banknote`, `Wallet` | Chọn phương thức thanh toán |
| Tra cứu đơn | `PackageSearch` hoặc `ClipboardList` | Trang `/orders`, chọn một |
| Bài hướng dẫn | `BookOpen` | Menu và thẻ bài guides |
| Liên hệ hỗ trợ | `Phone`, `Mail`, `MapPin` | Footer và trang liên hệ nếu có |

Icon phải có kích thước đồng nhất theo vị trí (header 20–24px, trong nút 16–20px), có `aria-label` khi là nút không có chữ, và trạng thái hover/focus rõ ràng.

### Animation và tương tác — quy định bắt buộc

Các hiệu ứng dưới đây suy ra từ hành vi đã quan sát trên web mẫu (hover, modal, toast, cập nhật giỏ). Thời lượng cụ thể là chuẩn Gemini phải áp dụng thống nhất, không phải số liệu lấy từ code nguồn của web mẫu.

- Thẻ sản phẩm khi hover: ảnh phóng nhẹ (scale khoảng 1.03–1.05), thẻ nâng nhẹ với bóng đổ, nút "Xem nhanh" và nút thêm giỏ hiện dần (opacity + trượt lên nhẹ). Thời lượng 200–300ms, easing `ease-out`.
- Modal Xem nhanh: nền mờ fade-in, hộp thoại scale từ khoảng 0.95 lên 1 và fade-in trong 200–250ms; đóng có hiệu ứng ngược ngắn, bấm ra ngoài hoặc phím Esc cũng đóng được.
- Toast: trượt vào từ góc dưới/trên phải kèm fade-in, tự tắt sau 2–3 giây, có nút đóng.
- Badge giỏ hàng: nảy nhẹ (scale pulse) mỗi khi số lượng thay đổi.
- Thanh tiến trình freeship: chiều rộng thay đổi có transition 300–500ms khi tổng giỏ thay đổi.
- Dropdown gợi ý tìm kiếm: fade-in + trượt xuống nhẹ 150–200ms khi có kết quả.
- Chuyển tab (chi tiết sản phẩm, đăng nhập/đăng ký): fade nội dung 150–200ms, không giật trang.
- Accordion/bộ lọc trên mobile: mở/đóng có transition chiều cao hoặc fade, nút lọc luôn bấm được.
- Trạng thái loading: dùng skeleton cho lưới sản phẩm và ảnh khi tải dữ liệu, không để trang trắng.
- Tôn trọng `prefers-reduced-motion`: người dùng tắt hiệu ứng chuyển động thì giảm/bỏ animation.
- Không dùng animation quá 500ms cho thao tác thường ngày, không dùng hiệu ứng làm chậm thêm giỏ, lọc hay thanh toán.

### Responsive và SEO

- Breakpoint cần kiểm tra khi nghiệm thu: khoảng 390px (điện thoại), khoảng 768px (tablet), khoảng 869px (màn hình hẹp đã phát hiện lỗi trên web mẫu), 1280px trở lên (desktop).
- Ở mọi breakpoint: header vẫn truy cập được tìm kiếm, giỏ hàng và menu danh mục; lưới sản phẩm không tràn ngang; modal Xem nhanh cuộn được và nút đóng luôn nhìn thấy.
- Mỗi trang có `title` và meta description riêng, không trùng lặp. Trang sản phẩm có canonical theo slug, Open Graph có ảnh sản phẩm, và dữ liệu cấu trúc Product (JSON-LD) nếu dữ liệu đủ.
- Có `sitemap.xml` liệt kê trang tĩnh, sản phẩm và bài guides; có `robots.txt` không chặn các trang bán hàng.
- Ảnh dùng `next/image`, có `alt` là tên sản phẩm/bài viết, không để ảnh lỗi: ảnh thiếu phải hiện placeholder có nhãn, không hiện icon ảnh gãy.

### Lỗi của web mẫu — tuyệt đối không lặp lại

Các lỗi sau đã kiểm chứng trên web mẫu. Bản clone mắc lại bất kỳ lỗi nào trong danh sách này là chưa đạt nghiệm thu.

1. Ô tìm kiếm biến mất hoàn toàn ở viewport hẹp khoảng 869px, không có icon mở lại và phím tắt không mở được tìm kiếm.
2. Giỏ hàng bị ghi đè khi mở nhiều tab: sản phẩm đã xóa "sống lại", sản phẩm mới thêm bị mất do tab cũ ghi đè state.
3. Số lượng đánh giá không nhất quán: đầu trang sản phẩm ghi "5.0 (15 đánh giá)" trong khi tab Đánh giá ghi 0 và báo chưa có đánh giá.
4. Tìm bài guides với từ "Rainy75" không ra kết quả dù có bài liên quan và chính placeholder gợi ý từ đó.
5. Lỗi chính tả trong tiêu đề trang chủ của web mẫu ("Khám Phá sssssssTheo...") — bản clone phải rà soát chính tả mọi tiêu đề trước khi bàn giao.
6. Đổi biến thể sản phẩm nhưng ảnh gallery không đổi theo biến thể (nếu biến thể có ảnh riêng).
7. Ảnh bìa một số bài guides bị lỗi không tải được.

## Tổng quan các task

| Task | Tên | Phụ thuộc | Trạng thái |
| --- | --- | --- | --- |
| 1A–1H | Clone frontend + backend phía khách hàng của giao diện Next.js | Không | Task đầu tiên, làm ngay |
| 2 | Thay dữ liệu mẫu bằng sản phẩm thật của The BABU Store | Xong Task 1 | Chờ danh sách sản phẩm thật |
| 3 | SEO, hiệu năng và rà soát nội dung | Xong Task 1 | Sau Task 1 |
| 4 | QA toàn site và sửa lỗi | Xong Task 1–3 | Sau Task 3 |
| 5 | Deploy lên hosting và trỏ tên miền thebabustore.vn | Xong Task 4 | Cần thông tin hosting |
| 6 | Clone/dựng phần Admin | Xong Task 5 | Giai đoạn sau, lệnh riêng |
| 7 | Phiếu bán hàng PDF/email và thông báo đơn hàng | Được duyệt riêng | Giai đoạn sau, lệnh riêng |

## Task 1 — Clone frontend và backend phía khách hàng

Mục tiêu chung: dựng được một website Next.js chạy local, có đủ các trang, chức năng, icon và animation như đặc tả khảo sát ở trên, dùng dữ liệu mẫu ngành gaming gear của The BABU Store. Admin không nằm trong task này.

Gemini thực hiện Task 1 theo đúng thứ tự 8 task con dưới đây. Mỗi task con là một lần ra lệnh riêng, làm xong phải **show màn hình trình diễn trực tiếp** (theo quy tắc Làm việc minh bạch), báo cáo và dừng.

### Task 1A — Khởi tạo project và khung layout

Việc phải làm:

- Khởi tạo Next.js App Router + TypeScript + Tailwind CSS, cài `lucide-react`.
- Dựng cấu trúc thư mục cho `app/`, `components/`, `lib/`, `data/` hoặc `prisma/` (nếu dùng Prisma) và file cấu hình môi trường mẫu `.env.example` không chứa mật khẩu thật.
- Dựng Header, Footer, thanh tìm kiếm với dropdown gợi ý (khung giao diện trước, dữ liệu mẫu tối thiểu), icon giỏ kèm badge, menu danh mục.
- Dựng hệ thống toast, modal nền và các component dùng chung: ProductCard, Price, Breadcrumb, Skeleton.

Nghiệm thu:

- Chạy `npm run dev` mở được trang chủ khung layout trên trình duyệt hiển thị.
- Header/Footer hiện đúng ở mọi trang, tìm kiếm có icon mở lại được ở màn hình 390px và khoảng 869px.
- Không có lỗi console nghiêm trọng; báo cáo liệt kê cây thư mục đã tạo.

### Task 1B — Trang chủ

Việc phải làm:

- Khối danh mục với nút "Xem ngay" dẫn đúng `/products?category=[slug]`.
- Khối sản phẩm bán chạy/nổi bật dùng ProductCard, có nhãn giảm giá, thêm giỏ nhanh, Xem nhanh (modal có thể hoàn thiện ở Task 1E, tại task này ít nhất mở được modal khung đúng nội dung).
- Các khối dịch vụ: bảo hành, freeship, lube mod với icon đúng quy định.

Nghiệm thu:

- Bấm từng thẻ danh mục đi đúng trang cửa hàng đã lọc.
- Thêm giỏ từ trang chủ hiện toast và badge giỏ tăng đúng.
- Trình diễn trực tiếp trên trình duyệt cho người dùng xem từng thao tác.

### Task 1C — Cửa hàng: tìm kiếm, lọc, sắp xếp

Việc phải làm:

- Trang `/products` đọc tham số URL `q`, `category`, `priceRange`, `sort` và lọc ở server/backend.
- Chip danh mục, sidebar lọc khoảng giá, 4 kiểu sắp xếp, nút "Đặt lại bộ lọc" giữ nguyên sort, dòng đếm số sản phẩm.

Nghiệm thu:

- Tìm một từ khóa có kết quả và một từ khóa không có kết quả đều hiển thị đúng.
- Lọc "Dưới 500.000₫" chỉ hiện sản phẩm dưới mức đó trong dữ liệu mẫu; sắp xếp giá tăng/giảm đúng thứ tự.
- Tải lại trang vẫn giữ bộ lọc theo URL.

### Task 1D — Chi tiết sản phẩm

Việc phải làm:

- Trang `/products/[slug]` với gallery + thumbnail, tồn kho, đánh giá, giá, SKU, chọn biến thể đổi SKU (và đổi ảnh nếu biến thể có ảnh riêng), chọn số lượng, Thêm vào giỏ, Mua Ngay (thêm vào giỏ rồi chuyển `/checkout`).
- 4 tab Mô tả / Thông số / Bảo hành / Đánh giá, form gửi đánh giá lưu được vào backend và số đếm đánh giá nhất quán ở mọi vị trí trên trang.
- Khối sản phẩm liên quan cùng danh mục.

Nghiệm thu:

- Đổi biến thể thấy SKU thay đổi tương ứng trên màn hình.
- Gửi một đánh giá thử, tải lại trang vẫn thấy và số đếm khớp nhau.
- Sản phẩm không tồn tại hiện trang 404 thân thiện.

### Task 1E — Xem nhanh, giỏ hàng và voucher

Việc phải làm:

- Modal Xem nhanh đầy đủ như đặc tả: gallery, biến thể, số lượng, Thêm Vào Giỏ, Mua Ngay, badge dịch vụ, đóng bằng X / Esc / bấm ra ngoài, animation đúng quy định.
- Trang `/cart`: tăng/giảm, xóa từng dòng, xóa tất cả, thanh tiến trình freeship ngưỡng 500.000₫ có animation, nhập/gỡ voucher qua backend kiểm tra.
- Giỏ lưu bền sau khi tải lại trang và đồng bộ đúng giữa các tab (lắng nghe thay đổi storage/sync, không ghi đè bằng state cũ).

Nghiệm thu:

- Mở 2 tab, thêm/xóa ở tab này thì tab kia cập nhật đúng, không có sản phẩm "sống lại".
- Voucher sai báo lỗi rõ; voucher mẫu đúng trừ đúng số tiền và không vượt trần.
- Giỏ dưới và trên 500.000₫ đều hiện đúng thông điệp freeship; trình diễn trực tiếp cả hai trường hợp.

### Task 1F — Thanh toán và tra cứu đơn hàng

Việc phải làm:

- Trang `/checkout`: form giao hàng có validate (họ tên, số điện thoại, địa chỉ bắt buộc), chọn tỉnh/thành, 3 phương thức VietQR / COD / MoMo, ô voucher và nút voucher nhanh, tóm tắt đơn, nút "Xác Nhận Đặt Hàng".
- Backend tạo đơn: tính lại tiền ở server, trừ voucher, tính phí ship/freeship, sinh mã đơn duy nhất không trùng, lưu đơn và làm trống giỏ sau khi đặt thành công, hiện trang/thông báo xác nhận kèm mã đơn.
- Trang `/orders`: tra cứu theo mã đơn, chip trạng thái, nút "Làm mới", trạng thái không tìm thấy.
- Không thêm nút xuất/in/tải hóa đơn trong task này.

Nghiệm thu:

- Đặt một đơn thử COD xem được mã đơn và tra cứu lại ra đúng đơn đó.
- Sửa giá ở phía client (nếu thử qua devtools) không làm thay đổi tổng tiền server tính.
- Thiếu số điện thoại hoặc địa chỉ thì không đặt được và báo lỗi tại đúng trường.

### Task 1G — Đăng nhập, đăng ký và guides

Việc phải làm:

- Trang `/login` 2 tab đăng nhập/đăng ký với validate, mật khẩu lưu dạng hash ở backend, đăng xuất, bảo vệ các trang cần tài khoản (nếu có).
- Trang `/guides` và `/guides/[slug]`: tìm kiếm bài (tìm được theo từ khóa trong tiêu đề, kể cả trường hợp tương tự "Rainy75"), lọc danh mục, thẻ bài có thời gian đọc, ảnh bìa có fallback khi lỗi.

Nghiệm thu:

- Đăng ký tài khoản thử, đăng xuất, đăng nhập lại thành công trên màn hình trình diễn.
- Tìm bài bằng từ khóa trong tiêu đề cho ra đúng bài; lọc danh mục cho đúng nhóm bài.
- Mật khẩu không xuất hiện dạng thô trong database hay log.

### Task 1H — Backend hoàn chỉnh, seed dữ liệu và tài liệu chạy local

Việc phải làm:

- Hoàn thiện toàn bộ API/Server Actions đã liệt kê ở mục Backend, chọn và ghi rõ một phương án database (ví dụ Prisma + SQLite cho local hoặc PostgreSQL khi deploy) kèm lệnh migrate/seed.
- Seed dữ liệu mẫu gaming gear đủ dùng để kiểm tra mọi chức năng: tối thiểu đủ 5 danh mục, sản phẩm có biến thể, sản phẩm dưới và trên 500.000₫, voucher mẫu, bài guides ở nhiều danh mục, 1 tài khoản mẫu local. Mọi seed phải ghi chú "dữ liệu mẫu".
- Viết `README.md` trong project: cách cài, cách chạy dev, cách seed lại dữ liệu, danh sách route và API.

Nghiệm thu:

- Từ thư mục sạch, làm theo README cài và chạy được toàn site.
- Mọi route trong sitemap mở được; API sản phẩm/voucher/đơn hàng trả đúng dữ liệu mẫu.
- Báo cáo tổng kết Task 1 đối chiếu từng mục đặc tả khảo sát: đã có / chưa có.

## Task 2 — Thay dữ liệu mẫu bằng sản phẩm thật

- Chỉ bắt đầu khi người dùng gửi danh sách sản phẩm thật của The BABU Store (tên, giá, ảnh, danh mục, biến thể, tồn kho, mô tả).
- Việc phải làm: nhập dữ liệu thật, xóa hoặc tắt toàn bộ seed mẫu, kiểm tra lại giá/SKU/ảnh từng sản phẩm, giữ nguyên chức năng đã nghiệm thu ở Task 1.
- Nghiệm thu: không còn sản phẩm mẫu nào hiển thị; số lượng sản phẩm khớp danh sách được gửi; ảnh không lỗi.

## Task 3 — SEO, hiệu năng và rà soát nội dung

- Việc phải làm: kiểm tra title/meta/canonical/Open Graph từng trang, sitemap, robots, JSON-LD sản phẩm; tối ưu ảnh và tốc độ tải; rà soát chính tả toàn bộ tiêu đề và nội dung hiển thị; kiểm tra responsive ở 390px, 768px, khoảng 869px và desktop.
- Nghiệm thu: không còn lỗi chính tả kiểu như lỗi "sssssss" của web mẫu; kiểm tra tốc độ bằng công cụ đo (ví dụ Lighthouse) và báo cáo điểm số thật, không tự nhận định khi chưa đo.

## Task 4 — QA toàn site và sửa lỗi

- Việc phải làm: chạy lại toàn bộ checklist của Task 1A–1H, cộng với danh sách "Lỗi của web mẫu — tuyệt đối không lặp lại". Kiểm tra trên trình duyệt hiển thị, thao tác thật từng chức năng, ghi lại lỗi kèm ảnh chụp màn hình.
- Nghiệm thu: mọi mục đạt hoặc có danh sách lỗi còn tồn đọng được người dùng chấp nhận bằng văn bản trong cuộc trò chuyện. Không tự tuyên bố "hết lỗi" khi chưa trình diễn.

## Task 5 — Deploy và trỏ tên miền

- Chỉ làm khi người dùng cung cấp thông tin hosting/VPS và quyền truy cập cần thiết qua kênh an toàn. Không yêu cầu gửi mật khẩu qua nội dung file này.
- Việc phải làm: build production, cấu hình biến môi trường, database production, deploy, kiểm tra lại toàn bộ chức năng trên bản production, trỏ tên miền `thebabustore.vn` và bật HTTPS.
- Lưu ý đã biết: tên miền quản lý tại TenTen, DNS đang dùng nameserver Cloudflare. Gemini phải kiểm tra trạng thái gia hạn tên miền và bản ghi DNS hiện tại trên màn hình hiển thị trước khi thay đổi bất cứ bản ghi nào, và báo trước mọi thay đổi DNS cho người dùng duyệt.
- Nghiệm thu: mở được site bằng tên miền thật qua HTTPS, đặt được đơn thử, tra cứu được đơn thử đó.

## Task 6 — Phần Admin (giai đoạn sau, chỉ làm khi có lệnh riêng)

- Task này **chưa được đặc tả chi tiết trong file này** vì người dùng đã quyết định để Admin làm sau. Khi người dùng ra lệnh, cần khảo sát và viết đặc tả Admin riêng rồi mới dựng.
- Không suy đoán trước tính năng Admin, không dựng sẵn khung Admin trong các task trước.

## Task 7 — Phiếu bán hàng và thông báo đơn hàng (giai đoạn sau, chỉ làm khi được duyệt riêng)

- Web mẫu không có xuất hóa đơn phía khách hàng, nên đây là tính năng dựng mới, không phải clone.
- Phạm vi đã trao đổi trước đó và đang chờ người dùng duyệt: phiếu bán hàng tự tạo (không phải hóa đơn thuế), tự sinh file PDF khi đơn được tạo/xác nhận/thanh toán, gửi email cho khách, bản in A5/80mm; thông báo trạng thái đơn qua email trước, Zalo là bước sau nếu được duyệt.
- Gemini không tự triển khai Task 7 khi chưa có lệnh duyệt rõ ràng.

## Mẫu prompt gửi Gemini cho từng task

Copy mẫu sau, thay `[1A]` bằng mã task cần làm:

> Đọc toàn bộ file workflow đính kèm trước khi làm. Chỉ làm Task [1A], không làm task khác. Tuân thủ mục "Quy tắc làm việc bắt buộc", đặc biệt: làm việc trực tiếp trên màn hình tôi nhìn thấy được — mở trình duyệt thao tác trên web mẫu https://nextjs.hgdigital.vn/ khi cần đối chiếu, mở bản local và bấm thử từng chức năng khi kiểm tra, không làm ngầm. Xong Task [1A] thì dừng, báo cáo đủ 4 mục theo quy định và chờ tôi nghiệm thu.

## Checklist nghiệm thu chung cho người dùng

Dùng checklist này sau mỗi task, trước khi cho Gemini làm task tiếp theo:

- Tôi có nhìn thấy Gemini thao tác trực tiếp trên màn hình trong suốt quá trình không, hay chỉ nhận được kết quả cuối cùng?
- Tôi có tự bấm thử được từng chức năng của task trên bản local không?
- Giá, voucher, freeship và tổng tiền có đúng với các con số trong đặc tả không (đặc biệt ngưỡng freeship 500.000₫)?
- Mở trên điện thoại hoặc thu hẹp trình duyệt, tìm kiếm và giỏ hàng còn dùng được không?
- Có lỗi nào trong danh sách "Lỗi của web mẫu — tuyệt đối không lặp lại" xuất hiện không?
- Báo cáo của Gemini có đủ 4 mục: đã làm gì, cách chạy thử, checklist đạt/chưa đạt kèm bằng chứng, vấn đề còn tồn tại?

Chỉ khi mọi câu trả lời đều đạt mới ra lệnh làm task tiếp theo.

## Nguồn khảo sát

- Web mẫu: https://nextjs.hgdigital.vn/ — khảo sát trực tiếp các trang `/`, `/products`, `/products/ghe-cong-thai-hoc-saigu-ares-l8`, `/cart`, `/checkout`, `/orders`, `/login`, `/guides` ngày 03-11-2026, kết hợp kết quả bấm thử từng chức năng (tìm kiếm, lọc, sắp xếp, xem nhanh, giỏ hàng, voucher, thanh toán, tra cứu đơn) đã thực hiện trước đó trong cùng dự án.
- Các mục ghi "suy luận" là cấu trúc suy ra từ đường dẫn và hành vi quan sát được, không phải thông tin lấy từ code nguồn của web mẫu. Gemini phải xác nhận lại các mục này khi dựng và báo nếu thực tế khác.

