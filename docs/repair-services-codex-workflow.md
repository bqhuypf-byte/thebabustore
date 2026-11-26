# Workflow Codex: trang dịch vụ sửa chữa gaming gear

Ngày lập: 25/11/2026. Dự án: The BABU Store. Đầu ra của yêu cầu hiện tại là tài liệu giao việc; chưa triển khai các thay đổi bên dưới.

## 1. Mục tiêu và cách thực hiện

Phát triển trang `/sua-chua-gear` thành danh mục dịch vụ rõ ràng cho chuột, bàn phím, tai nghe; giữ nhóm tay cầm đang có để không mất nội dung. Tham khảo cách chia mục của RightFix, nhưng dùng nhận diện BABU, Roboto, màu #182ABB và header/footer hiện tại.

Codex phải tự khảo sát nguồn công khai, thu thập dữ liệu có dẫn chứng, chuẩn hóa mục, thiết kế, triển khai và tự kiểm tra đến khi đạt checklist. Không dừng ở việc tạo giao diện với dữ liệu mẫu. Không cần hỏi lại những thao tác đọc nguồn, sửa code và kiểm tra local đã được giao. Chỉ hỏi khi thật sự thiếu thông tin kinh doanh mà không thể tìm trong dự án.

Thứ tự: khảo sát dự án → cào dữ liệu tham khảo → đối chiếu giá linh kiện trên Google → chuẩn hóa dữ liệu → bố trí giao diện → triển khai → kiểm tra trình duyệt → sửa lỗi → báo cáo. Nếu một nguồn bị chặn, tiếp tục phần độc lập và ghi rõ thiếu sót; không tự điền dữ liệu suy đoán.

## 2. Quy tắc giá bắt buộc

Phân biệt ba loại thông tin:

| Loại | Cách dùng |
| --- | --- |
| Giá nguồn RightFix | Lưu nguyên số và URL trong dữ liệu nghiên cứu, gắn nhãn giá của nguồn tham khảo. Không mặc định là giá BABU hoặc giá linh kiện rời. |
| Giá linh kiện rời | Bắt buộc tìm Google và mở trang bán đúng mã/biến thể; ghi nguồn, đơn vị và ngày kiểm tra. Chỉ hiển thị khi đã xác minh; ghi rõ chưa bao gồm công thay nếu nguồn không bao gồm. |
| Chi phí chẩn đoán/sửa chữa | Hiển thị “Báo giá sau kiểm tra”. Không gán một mức tiền cố định cho lỗi mạch, sensor, chập, vào nước, loạn phím hoặc lỗi chưa xác định. |

Giá dịch vụ “thay switch” của đối thủ có thể bao gồm công và vật tư, không được lấy làm giá một switch. Không suy ngược tiền linh kiện bằng cách trừ một mức công tự đặt. Giá vệ sinh chỉ hiện khi có biểu phí BABU trong dữ liệu dự án; nếu chưa có thì liên hệ xác nhận phạm vi và chi phí. Không dùng 0đ, “miễn phí”, giá “từ” hoặc giá gạch ngang để thay cho giá chưa biết.

Khi hiển thị linh kiện, lời nhắc: “Giá linh kiện tham khảo, chưa bao gồm công thay. Tổng chi phí được xác nhận sau khi kiểm tra thiết bị.” Nếu BABU có giá bán đã xác nhận, dùng nhãn “Giá linh kiện” và lưu tách biệt với giá thị trường. Không tự biến giá thị trường thành cam kết bán của cửa hàng.

## 3. Bối cảnh dự án phải đọc trước khi code

- `AGENTS.md` và mọi hướng dẫn áp dụng trong thư mục định sửa.
- `docs/repair-service-layout-audit-2026-11-24.md`.
- Route hiện có: `routes/web.php`, tên route `repair-services`.
- Nội dung cấu trúc: `app/Support/RepairServicesContent.php`.
- Component: `app/Livewire/RepairServices.php`; tìm view thực tế từ hàm render.
- Luồng gửi yêu cầu: `app/Mail/RepairRequest.php` và view email liên quan.
- Kiểm thử hiện có: `tests/Feature/RepairServicesTest.php`.

Giữ route và luồng gửi yêu cầu hiện tại. Không tạo một app Next.js hoặc một hệ thống checkout dịch vụ mới. Không sửa sản phẩm, tồn kho, giỏ hàng hoặc mô tả hàng hóa trong nhiệm vụ này. Nếu công việc sau mở rộng sang nội dung sản phẩm, phải đọc skill và workflow SEO trong AGENTS.md trước khi sửa.

## 4. Bằng chứng nguồn và giới hạn khảo sát

Nguồn chính:

- [Danh mục RightFix, trang 1](https://rightfix.com.vn/dich-vu-sua-chua-gia-tot).
- [Danh mục RightFix, trang 2](https://rightfix.com.vn/dich-vu-sua-chua-gia-tot/page/2).
- [Danh mục RightFix, trang 3](https://rightfix.com.vn/dich-vu-sua-chua-gia-tot/page/3).

Trang 1 và 3 đã mở trên trình duyệt ngày lập tài liệu; trang 2 đọc qua công cụ web, có thể là bản lưu cũ, cần mở lại khi triển khai. Giá dưới đây là số hiển thị ở thẻ danh mục; chưa xác minh đơn vị, biến thể, công thay, tình trạng áp dụng hoặc giá cuối cùng ở toàn bộ trang chi tiết. Chưa thử bộ lọc, sắp xếp, quick view hay giỏ hàng. Không mô tả các chức năng đó là đã kiểm tra.

Không cần đăng nhập để đọc các danh mục trên. Chưa có tài khoản hoặc phiên đăng nhập được cung cấp, nên chưa khảo sát khu vực riêng. Nếu cần trang riêng, dùng phiên được chủ tài khoản cho phép; không tạo tài khoản hoặc tìm cách vượt kiểm soát truy cập. Không lưu mật khẩu, cookie hay dữ liệu khách hàng vào tài liệu.

Trình duyệt nguồn có báo lỗi JavaScript `woof_ext_init_functions is not defined` và phần phân trang có liên kết ngoại ngữ không liên quan. Chỉ thu dữ liệu dịch vụ đúng phạm vi, không sao chép script hoặc các liên kết lạ đó.

### Danh mục và giá thẻ đã thu thập

Các nhãn dưới đây được rút gọn. Đơn vị tiền: VNĐ. P1/P2/P3 trỏ đến ba URL ở trên. Dữ liệu trùng giữa các trang đã gộp. Đây là bảng nghiên cứu đối thủ, không phải bảng giá BABU.

| Nhóm | Mục nguồn, rút gọn | Giá nguồn | Trang |
| --- | --- | ---: | --- |
| Chuột | Dây/cáp | 150.000 | P1, P2 |
| Chuột | Vệ sinh/feet/skin/sơn, thẻ gộp | 100.000 | P1 |
| Chuột | Mạch/via | 80.000 | P1 |
| Chuột | Cổng | 120.000 | P1, P2 |
| Chuột | Pin | 150.000 | P1, P2 |
| Chuột | Vỏ/trục lăn | 50.000 | P1 |
| Chuột | Encoder | 100.000 | P3 |
| Chuột | Switch click | 90.000 | P3 |
| Bàn phím | Vệ sinh/phục hồi/sơn, thẻ gộp | 180.000 | P1 |
| Bàn phím | Mạch | 80.000 | P1 |
| Bàn phím | Cổng | 120.000 | P1, P2 |
| Bàn phím | Pin | 200.000 | P1, P2 |
| Bàn phím | Dây/cáp | 150.000 | P2, P3 |
| Bàn phím | LED/mod | 50.000 | P2, P3 |
| Bàn phím | Lube | 3.000 | P3 |
| Bàn phím | Hàn lắp | 150.000 | P3 |
| Bàn phím | Rã hàn | 200.000 | P3 |
| Bàn phím | Romer-G | 90.000 | P3 |
| Bàn phím | Switch | 70.000 | P3 |
| Tai nghe | Cổng | 150.000 | P1, P2 |
| Tai nghe | Dây/cáp | 150.000 | P2 |
| Tai nghe | Đệm đầu | 350.000 | P2 |
| Tai nghe | Đệm tai | 300.000 | P2 |
| Tay cầm | Analog | 80.000 | P1 |
| Tay cầm | Nút vai/cò | 90.000 | P2 |

Có 25 mục khác nhau trong bảng sau khi gộp. Không ép dữ liệu nguồn khớp số mục dịch vụ đang có trong dự án: đối chiếu từng mục và ghi rõ phần giữ, bổ sung hoặc gộp. Sensor là yêu cầu của chủ dự án, chưa thấy thẻ giá riêng trong phạm vi nguồn đã đọc.

## 5. Luồng tự thu thập và đối chiếu

1. Mở lại cả ba trang, lần theo liên kết dịch vụ chi tiết trong cùng phạm vi. Thu URL chuẩn, tên mục, nhóm, giá hiển thị, biến thể, đơn vị, bao gồm công hay chưa, ghi chú tương thích, ngày giờ và cách truy cập. Không suy diễn giá thẻ là giá tất cả biến thể.
2. Đọc cấu trúc hiển thị thay vì sao chép nguyên HTML/script. Giới hạn tốc độ, cache kết quả, không lặp vô hạn; không vượt CAPTCHA. Tách dữ liệu nguồn thô với dữ liệu chuẩn hóa; chỉ lưu phần cần thiết cho nghiên cứu.
3. Tìm Google theo từng mã linh kiện: ví dụ `Kailh GM 8.0 giá switch lẻ`, `TTC encoder 9mm giá`, `Huano switch chuột giá`, `earpad [model] giá`, `switch bàn phím [mã] giá`. Với pin, cổng, dây, sensor phải khớp model/thông số trước khi so sánh.
4. Mở trực tiếp trang bán; snippet Google chỉ là đầu mối. Ưu tiên hai nguồn bán độc lập cho mỗi mã. Nếu chỉ có một nguồn, ghi độ tin cậy hạn chế, không bịa nguồn thứ hai. Loại giá voucher, trả góp, đặt cọc, combo không rõ đơn vị hoặc biến thể rẻ nhất không đúng mã.
5. Chuẩn hóa đơn vị một chiếc/cặp/bộ, tiền tệ, VAT/phí vận chuyển nếu nguồn có ghi. Giữ số gốc và cách quy đổi. Không trộn giá công thay với giá linh kiện; không lấy trung bình các mã khác nhau.
6. Trước khi đưa giá lên local, kiểm tra lại URL và biến thể. Nếu giá mâu thuẫn hoặc không còn truy cập được, dùng trạng thái chưa xác minh và không hiển thị số đó như giá chính thức.
7. Xuất báo cáo nguồn, danh mục chuẩn hóa và bảng đối chiếu. Mỗi giá hiển thị phải truy ngược được đến chứng cứ hoặc biểu giá BABU đã có.

Đầu mối Google đã tìm được ngày lập tài liệu: [MODGEAR trên Shopee](https://shopee.vn/modgear?entryPoint=ShopByPDP), có kết quả về switch Kailh và encoder. Chưa mở xác minh từng sản phẩm/biến thể nên chưa đủ điều kiện điền giá linh kiện. [FIXORY Lab](https://fixorylab.vn/services/sua-chuot-gaming) là nguồn dịch vụ để đối chiếu phân loại; không dùng giá gói của họ làm giá linh kiện rời.

## 6. Danh mục dịch vụ đề xuất

Đây là cấu trúc thiết kế từ yêu cầu chủ dự án, không phải xác nhận BABU nhận mọi loại sửa chữa. Đối chiếu nội dung hiện có; mục chưa xác nhận năng lực dùng trạng thái nháp, không cam kết nhận sửa hoặc thay sensor trên mọi model.

| Thiết bị | Bảo dưỡng | Thay linh kiện | Chẩn đoán/sửa lỗi |
| --- | --- | --- | --- |
| Chuột | Vệ sinh, feet | Switch click, encoder, bánh xe, dây, pin, cổng | Double click, giữ kéo bị ngắt, loạn cuộn, mạch/via, mất kết nối, sensor không nhận/tracking bất thường |
| Bàn phím | Vệ sinh, bảo dưỡng stab/lube | Switch, stab, LED, dây, cổng, pin, socket | Double/chatter, liệt/loạn phím, chập hàng/cột, vào nước, lỗi mạch/kết nối |
| Tai nghe | Vệ sinh | Earpad, headband, dây, jack/cổng, mic hoặc pin theo model | Mất một bên, rè, mic không nhận, chập chờn, mạch, gãy kết cấu |
| Tay cầm | Giữ nhóm đang có | Analog, nút theo phạm vi đã xác nhận | Theo nội dung dự án; ưu tiên thấp hơn ba nhóm trên |

Một triệu chứng có nhiều nguyên nhân. Không mặc định double click là chắc chắn thay switch; không mặc định lỗi tracking là cần thay sensor. Không hứa tương thích linh kiện quang/cơ/hall-effect hoặc pin khác thông số.

## 7. Bố trí trang và hành vi

1. Hero ngắn: sửa chữa và bảo dưỡng gaming gear; CTA “Gửi tình trạng thiết bị”. Dùng ảnh BABU có sẵn hoặc ảnh được phép sử dụng, không dùng ảnh đối thủ làm bằng chứng tay nghề.
2. Chọn thiết bị: Chuột / Bàn phím / Tai nghe / Tay cầm. Giữ nhóm đã chọn khi tìm kiếm, có trạng thái không có kết quả.
3. Chọn nhu cầu: Vệ sinh / Thay linh kiện / Sửa lỗi. Danh sách lỗi dùng từ khách dễ hiểu, hỗ trợ tìm “double”, “loạn”, “sensor”, “mất một bên”.
4. Thẻ dịch vụ: tên, thiết bị, triệu chứng ngắn, kiểu giá và nút xem chi tiết/gửi yêu cầu. Không đặt nút mua sửa mạch như sản phẩm giá cố định.
5. Bảng linh kiện: loại/mã, biến thể, đơn vị, tương thích đã xác minh, giá tham khảo và ngày cập nhật. Thiếu giá dùng “Liên hệ xác nhận”. Không cần tài khoản để xem.
6. Chi tiết dịch vụ dùng accordion hoặc modal phù hợp code hiện tại: triệu chứng, phạm vi, linh kiện có thể dùng, cách báo giá, CTA điền sẵn thiết bị/dịch vụ vào form. Nếu dùng modal phải đóng bằng Escape, quản lý focus và cuộn trên mobile.
7. Quy trình: nhận thông tin → nhận và kiểm tra thiết bị → gửi phương án cùng chi phí → khách đồng ý → sửa và test → bàn giao. Chỉ mô tả chính sách thời gian, vận chuyển, bảo hành, phí kiểm tra khi dự án có dữ liệu xác nhận.
8. FAQ và form tiếp nhận hiện tại. Không copy hotline, địa chỉ, bảo hành, đánh giá hoặc lời cam kết của RightFix sang BABU.

Desktop ưu tiên lưới 3–4 cột theo không gian thực tế; mobile 1–2 cột khi đọc đủ rõ. Bộ lọc mobile dễ bấm, bảng linh kiện không gây tràn toàn trang. Mục dài thu gọn có nhãn rõ, không giấu mất CTA và thông tin báo giá.

## 8. Dữ liệu và admin

Không nhét giá vào câu văn HTML hoặc hard-code lặp trong nhiều view. Tái sử dụng lớp nội dung hiện có; nếu cần admin chỉnh linh kiện/dịch vụ, bổ sung model/form phù hợp hệ thống hiện tại, không tạo admin song song. Tách bảng nghiên cứu nguồn khỏi nội dung hiển thị.

Trường tối thiểu:

- Dịch vụ: id/slug, nhóm thiết bị, loại nhu cầu, tên, triệu chứng, mô tả cấu trúc, thứ tự, trạng thái xuất bản, kiểu giá (`inspection_required`, `reference_part`, `confirmed_price`).
- Linh kiện: mã, biến thể, đơn vị, tương thích, giá tham khảo, giá BABU nếu đã xác nhận, tiền tệ, công đã gồm hay chưa, trạng thái xác minh, ngày kiểm tra.
- Nguồn: URL, tên nhà bán, giá gốc, đơn vị gốc, biến thể, thời điểm, ghi chú và mức tin cậy. Một linh kiện có nhiều nguồn.

Với `inspection_required`, giá hiển thị phải null; không vô tình render 0đ hoặc dữ liệu có cấu trúc Offer giá 0. Giữ nội dung gốc để đối chiếu và chỉnh sửa. Dùng chính sách upload/validation có sẵn nếu cần ảnh; không tự mở upload tùy ý.

## 9. Form và báo giá

Luồng khách: chọn dịch vụ → form được điền sẵn → nhập thông tin liên hệ, hãng/model và mô tả → kiểm tra lỗi → gửi → thông báo thành công hoặc lỗi gửi. Dùng trường bắt buộc hiện có; chỉ bổ sung trường thật sự cần. Không tự thêm trường địa chỉ nếu chưa có nhu cầu giao nhận.

Luồng vận hành: kỹ thuật viên xem tình trạng thực tế, xác nhận linh kiện và tiền công, gửi báo giá để khách đồng ý trước khi sửa. Phần này là quy trình nghiệp vụ; không mặc định phải xây CRM/tracking hoặc tự động gửi báo giá nếu chưa nằm trong tính năng hiện tại. Không gửi yêu cầu thật hoặc email thử tới người khác khi kiểm tra; dùng mail fake/local.

## 10. Kiểm tra bắt buộc và tiêu chí hoàn thành

- Chạy test dịch vụ hiện có, bổ sung test có ý nghĩa cho lọc, kiểu giá và validation mới; không cố giữ số lượng nhóm/mục cứng nếu danh mục đã thay đổi hợp lý.
- Kiểm tra `/sua-chua-gear` bằng trình duyệt ở 1440px và 390px; chụp ảnh kết quả, kiểm tra font, ảnh hỏng, tràn ngang, khoảng cách và CTA.
- Thử lọc thiết bị, tìm từ khóa, kết quả trống, accordion/modal, bàn phím, điền sẵn form và lỗi nhập liệu. Chạy thử gửi bằng môi trường fake; không làm phát sinh yêu cầu thật.
- Xác nhận mọi giá linh kiện có URL nguồn trực tiếp, đúng đơn vị/biến thể và ngày kiểm tra; số chưa xác minh không được xuất bản như giá chính thức.
- Xác nhận các lỗi mạch/sensor/vào nước/loạn chưa chẩn đoán hiển thị “Báo giá sau kiểm tra”; không hiện số của RightFix làm giá BABU.
- Kiểm tra admin sửa nội dung/giá cập nhật đúng storefront; quyền truy cập và validation giá hoạt động. Nếu chưa triển khai admin, nêu rõ chưa đạt mục này.
- Không làm hỏng trang sản phẩm, header/footer, form cũ; không đổi dữ liệu stock hay giá hàng hóa.
- Báo lỗi console/network do thay đổi gây ra; sửa và thử lại đúng luồng trước khi tuyên bố hoàn tất.

## 11. Đầu ra Codex phải bàn giao

1. Code và nội dung dịch vụ hoàn chỉnh trong dự án hiện tại khi được giao triển khai.
2. `docs/repair-services-source-audit.md`: các URL thực tế đã đọc, mục/giá thu được, nguồn chặn và phạm vi chưa kiểm tra.
3. `docs/repair-parts-price-research.csv` hoặc JSON: mỗi biến thể, giá, đơn vị, nguồn, ngày, trạng thái; dùng định dạng máy đọc được.
4. `docs/repair-services-qa.md`: checklist đạt/chưa đạt, kết quả test và đường dẫn ảnh desktop/mobile.
5. Báo cáo ngắn về file đã sửa, cách mở local, dữ liệu giá còn thiếu và giới hạn thực tế. Không báo “clone đầy đủ” nếu chưa khảo sát hết trang chi tiết hoặc vùng đăng nhập.

## 12. Prompt giao việc sẵn dùng

> Đọc `AGENTS.md` và `docs/repair-services-codex-workflow.md`, sau đó triển khai cải tiến trang `/sua-chua-gear` theo workflow. Tự khảo sát RightFix và các trang phân trang/chi tiết công khai, thu dữ liệu có nguồn, tìm Google rồi mở trực tiếp trang bán để đối chiếu từng giá linh kiện. Giữ giá nguồn tách biệt với giá BABU; lỗi sửa chữa cần chẩn đoán phải ghi “Báo giá sau kiểm tra”. Tái sử dụng Laravel/Livewire và nhận diện BABU hiện tại, bảo toàn form gửi yêu cầu. Hoàn thành giao diện, dữ liệu có thể chỉnh trong admin, kiểm tra desktop/mobile và các luồng tương tác, sửa lỗi, rồi bàn giao báo cáo nguồn và QA. Không tự bịa giá, tương thích, tồn kho, bảo hành hoặc cam kết thời gian. Nếu nguồn thiếu, tiếp tục phần độc lập và ghi rõ phần chưa xác minh.