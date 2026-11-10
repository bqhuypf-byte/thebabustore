# product_description_seo — Viết mô tả chi tiết sản phẩm chuẩn SEO từ ảnh + dữ liệu Google

## Khi nào dùng

- Dùng khi user gửi ảnh sản phẩm (ảnh thật, ảnh bảng thông số, ảnh bao bì, hoặc screenshot trang sản phẩm) và muốn có phần "Mô tả chi tiết" + bảng thông số + gạch đầu dòng nổi bật, chuẩn SEO, để đưa lên trang sản phẩm của shop.
- Đầu ra là 1 file Markdown cho 1 sản phẩm, dùng được ngay cho CMS hoặc đưa Codex đưa vào template trang chi tiết. Cấu trúc bài khớp với mẫu đã duyệt ở trang KZ Pisces: khối chữ + ảnh xen kẽ, bảng so sánh, bảng thông số.
- Không dùng skill này để viết bài blog, bài hướng dẫn hay mô tả cho sản phẩm chưa có ảnh và chưa có tên model rõ ràng. Thiếu 2 thứ đó thì hỏi user trước.

## Phạm vi áp dụng — toàn dự án, mọi sản phẩm

- Skill này áp dụng cho toàn bộ dự án thebabustore và mọi sản phẩm của shop, mọi danh mục (tai nghe, bàn phím, chuột, ghế, phụ kiện...), không giới hạn theo hãng hay loại sản phẩm.
- Mọi sản phẩm dùng chung một giọng văn, một chuẩn meta và một quy tắc ghi nguồn, để các trang sản phẩm của shop đồng nhất với nhau.
- Mỗi sản phẩm có 1 file riêng, đặt tên theo slug, lưu tại `~/workspace/your_files/mo-ta-san-pham-seo/<slug>.md` (và `<slug>.html` khi user yêu cầu bản HTML). Không gộp nhiều sản phẩm vào chung 1 file.
- Chạy hàng loạt: khi user gửi danh sách nhiều sản phẩm (tên + ảnh từng cái), làm lần lượt từng sản phẩm, mỗi sản phẩm 1 file riêng và 1 lần kiểm tra checklist riêng.
- Đầu ra khớp với template trang chi tiết dùng chung của shop (`ProductDetail`, xem file nhiệm vụ Codex): khối mô tả chi tiết, gạch đầu dòng tối đa 5 ý render bằng vòng lặp, bảng thông số — để Codex đưa thẳng vào 1 template duy nhất dùng cho mọi sản phẩm, không viết riêng cho từng sản phẩm.
- Ví dụ mẫu đã chạy qua skill này: `references/examples/kz-zsn-pro-x.md` (tai nghe) và `references/examples/ban-phim-co-mchose-k99-v3.md` (bàn phím). Đọc 2 file này trước khi viết cho sản phẩm mới để giữ đúng cấu trúc và độ chi tiết.

## Đầu vào — thu thập trước khi làm

1. **Tên sản phẩm đầy đủ** (kèm model, đời, mã SKU nếu có). Tên model chính xác là chìa khóa để tìm dữ liệu trên Google, nên xác nhận lại với user nếu tên bị mờ hoặc viết tắt.
2. **Ảnh sản phẩm:** ảnh thật của sản phẩm, ảnh bảng thông số (nếu có), ảnh bao bì/hộp, ảnh chi tiết (cổng kết nối, driver, phụ kiện). Ảnh càng rõ càng tốt. Chữ trong ảnh phải đọc được mới trích được thông số.
3. **Dữ liệu shop đã có:** giá, giá gốc, biến thể (màu/mẫu/mic...), tồn kho, danh mục, sản phẩm liên quan. Giá, SKU và tồn kho chỉ lấy từ đây, không lấy từ Google.
4. **Từ khóa chính** (nếu user đã có). Nếu chưa có, tự xác định ở Bước 4.
5. **Giọng văn và ngôn ngữ:** mặc định tiếng Việt, giọng bán hàng trực tiếp, câu ngắn, không hoa mỹ.

## Quy trình 5 bước

### Bước 1 — Đọc ảnh sản phẩm

- Xem từng ảnh và ghi lại: sản phẩm là gì, màu sắc, chất liệu, cổng kết nối, nút bấm, phụ kiện trong hộp, chữ in trên sản phẩm và bao bì.
- Đọc mọi chữ trong ảnh (bảng thông số, tem, nhãn) và trích nguyên văn số liệu, ghi rõ lấy từ ảnh nào.
- Ghi lại những gì ảnh KHÔNG cho thấy (ví dụ: không thấy mặt sau, không đọc được dòng chữ nhỏ). Những chỗ đó phải tìm ở Bước 2, không đoán.
- Nếu ảnh mờ hoặc chữ trong ảnh không đọc được: nói với user ngay và hỏi ảnh rõ hơn. Không viết mô tả dựa trên ảnh mờ.

### Bước 2 — Tìm và đối chiếu dữ liệu trên Google

- Tìm theo tên model chính xác bằng `browser.search`, rồi đọc trang nguồn bằng `browser.open` khi cần. Ưu tiên nguồn theo thứ tự: trang chính thức của hãng, trang của nhà phân phối lớn, bài đánh giá uy tín, trang bán hàng khác.
- Thu thập: thông số kỹ thuật đầy đủ, tính năng chính, câu hỏi người dùng hay tìm về sản phẩm này. Giá trên Google chỉ dùng để đối chiếu, không dùng làm giá bán.
- Đối chiếu 3 nguồn: ảnh sản phẩm, dữ liệu Google, dữ liệu shop. Lập bảng đối chiếu: khớp nhau, mâu thuẫn, hoặc chỉ có ở 1 nguồn.
- Khi mâu thuẫn: tin trang chính thức của hãng trước, rồi đến ảnh thật của sản phẩm, rồi đến shop khác. Ghi rõ đã chọn nguồn nào và vì sao. Không tự hòa giải bằng cách bịa một con số ở giữa.
- Sản phẩm ít dữ liệu (hàng custom, hàng mới, hàng ít người bán): nói rõ ngay với user. Mô tả sẽ dựa chủ yếu vào ảnh và thông tin shop cung cấp, phần thông số chỉ ghi những gì xác minh được.

### Bước 3 — Phân tích và chốt bảng thông số

- Lập bảng thông số từ các nguồn đã xác minh. Mỗi dòng ghi rõ nguồn: hãng, ảnh, shop, hoặc chưa xác nhận.
- Quy tắc cứng: không bịa thông số. Thông số nào không xác minh được thì bỏ khỏi bảng và ghi vào mục "Chưa xác nhận" trong file đầu ra để user bổ sung. Không viết số ước lượng vào bảng.
- Chuẩn hóa đơn vị và cách viết (Hz, kHz, Ω, mm, mW, giờ, gram) cho nhất quán với các sản phẩm khác của shop.
- Nếu sản phẩm có nhiều đời hoặc biến thể (như KZ ZSN Pro / Pro X / Pro 2): làm bảng so sánh các đời, đánh dấu cột của đời đang viết.

### Bước 4 — Nghiên cứu từ khóa và viết chuẩn SEO

- Từ khóa chính: tên sản phẩm + danh mục (ví dụ: "tai nghe KZ ZSN Pro X"). Từ khóa phụ: lấy từ gợi ý tìm kiếm và từ các trang đối thủ đang xếp hạng (giá, thông số, đánh giá, so sánh, mua ở đâu).
- Đặt từ khóa chính vào: tiêu đề H1, đoạn mở đầu (trong 100 chữ đầu), 1-2 tiêu đề nhỏ, meta title, meta description. Không nhồi từ khóa: nhắc tự nhiên, mỗi đoạn chỉ nhắc khi cần.
- Meta title 50-60 ký tự, meta description 140-160 ký tự, có tên sản phẩm, 1 lợi ích chính và tên shop.
- Viết theo cấu trúc trong `references/output-template.md`: đoạn mở đầu ngắn, các khối tính năng (tiêu đề + 2-3 câu + ảnh minh họa), bảng so sánh (nếu có nhiều đời), bảng thông số, 2-3 câu hỏi thường gặp (lấy từ câu hỏi thật người dùng tìm kiếm), gạch đầu dòng nổi bật tối đa 4-5 ý ngắn.
- Giọng văn: tiếng Việt tự nhiên, câu ngắn, nói lợi ích cho người mua (nghe được gì, dùng được cho việc gì). Không chép nguyên văn quảng cáo của hãng, không dùng từ hoa mỹ sáo rỗng.
- Link nội bộ: nhắc tên 2-3 sản phẩm liên quan của shop (lấy từ dữ liệu shop, không bịa tên sản phẩm).

### Bước 5 — Xuất file và kiểm tra trước khi giao

- Xuất 1 file Markdown cho 1 sản phẩm, đặt tên theo slug sản phẩm, lưu vào `~/workspace/your_files/mo-ta-san-pham-seo/<slug>.md`.
- Nếu user yêu cầu file HTML, xuất thêm bản `.html` standalone cùng nội dung (có `<title>` và `<meta name="description">` đúng chuẩn SEO, font Roboto theo phong cách shop, chỉ dùng ảnh thật của sản phẩm; bản HTML là phần mô tả để đăng lên trang, còn bản `.md` giữ đầy đủ nguồn tra cứu và mục "Chưa xác nhận").
- File gồm: meta title và meta description, gạch đầu dòng nổi bật, phần mô tả đầy đủ theo mẫu, bảng thông số kèm cột nguồn, mục "Chưa xác nhận", danh sách nguồn đã tra cứu.
- Chạy checklist trong `references/seo-checklist.md` trước khi giao: không có thông số bịa, từ khóa đúng vị trí, độ dài meta đúng, gạch đầu dòng không quá 5, giá và SKU lấy từ dữ liệu shop.
- Giao file kèm tóm tắt ngắn bằng tiếng Việt: đã tra cứu những nguồn nào, thông số nào chưa xác nhận, từ khóa chính đã dùng.

## Quy tắc không bao giờ phá

- Không bịa thông số, giá, tính năng, đánh giá hay số người mua. Không có dữ liệu thì bỏ trống và ghi "chưa xác nhận".
- Giá, SKU, tồn kho, link mua: chỉ lấy từ dữ liệu shop. Google chỉ dùng để đối chiếu thông số và nghiên cứu từ khóa.
- Không copy nguyên văn bài mô tả của shop khác hay của hãng. Viết lại bằng lời của shop, theo cấu trúc đã duyệt.
- Mỗi thông số trong bảng phải có nguồn. Không có nguồn thì không đưa vào bảng.
- Ảnh minh họa trong mô tả phải là ảnh thật của sản phẩm (ảnh user gửi hoặc ảnh trong repo shop). Không lấy ảnh từ Google về gắn vào.

## Available connectors

- Use `muse.connector_search` to find other connectors and `muse.skill_search` to find related skills.
- If nothing here fits, look on the web for a maintained API or MCP server and set it up with [Custom Connectors and MCPs](/opt/muse/skills/custom-connector-setup/SKILL.md), or build a custom connector.

## Websites

- [Google](https://www.google.com/) for product specs, reviews and keyword research, through `browser.search` and `browser.open`. Search results find sources; they never confirm a price, stock level or a completed action. Always open the source page before trusting a spec from a snippet.
- The manufacturer's official site for the product, for the authoritative spec sheet. Find it through search; never guess its URL from the brand name.
