# Lam ro noi dung do an - he thong ban hang MealKit

Nguon doi chieu: `README.md`, `routes/web.php`, `routes/admin.php`, `app/Http/Controllers`, `app/Models`, `app/Services`, `database/migrations`, `database/seeders`, `resources/views`.

Ten he thong: He thong thuong mai dien tu ban meal-kit/thuc pham so che san.

Doi tuong su dung:
- Khach hang: xem san pham, them gio hang, dat hang, thanh toan, theo doi don, danh gia san pham.
- Quan tri vien/nhan vien: quan ly danh muc, san pham, don hang, ma giam gia, lien he, nguoi dung.
- Nhan vien giao hang: xem don duoc phan cong, cap nhat trang thai giao hang.

Cong nghe chinh: Laravel 11, PHP, Blade, Eloquent ORM, MySQL/MariaDB, PayPal, VietQR, Gemini API, Python API goi y/tim kiem.

## Buoc 1. Ngu canh va bai toan

He thong giai quyet bai toan ban meal-kit online: khach hang can mua bo nguyen lieu da so che, dinh luong san, co thong tin khau phan, thoi gian nau, calo, bao quan va huong dan nau an.

Neu ban bang tin nhan, dien thoai hoac ghi chep thu cong thi kho dong bo danh muc, ton kho, gio hang, dia chi giao hang, thanh toan, trang thai don va lich su giao hang.

Pham vi he thong tap trung vao quy trinh thuong mai dien tu: xem san pham, gio hang, dat hang, thanh toan, quan ly don, khuyen mai, giao hang va tuong tac khach hang. He thong khong quan ly nhap kho tu nha cung cap, ke toan noi bo, POS tai quay hay ung dung mobile rieng.

Minh chung:
- `README.md` mo ta MealKit la nen tang ban thuc pham so che san, ho tro gio hang, thanh toan, danh gia, goi y san pham va admin dashboard.
- `database/seeders/CategorySeeder.php` tao 5 nhom meal-kit: Mon gia dinh, Eat clean, Mon nhanh 15 phut, Lau/Nuong, Combo tiet kiem.
- `database/migrations/2026_06_25_000001_add_meal_kit_details_to_products.php` bo sung du lieu dac thu meal-kit: khau phan, thoi gian so che, thoi gian nau, calo, bao quan, han su dung, nguyen lieu va cac buoc nau.

## Buoc 2. Van de cot loi

Van de chinh la can mot quy trinh ban hang khep kin tu luc khach chon meal-kit den khi don duoc giao va hoan tat.

Cac bat cap neu xu ly thu cong:
- De ban vuot ton kho vi khong kiem tra so luong khi khach them vao gio.
- De tinh sai tien neu co ma giam gia, phi giao hang hoac gia sale.
- Kho theo doi don dang o buoc nao: cho xac nhan, dang so che, da dong goi, dang giao hay da hoan thanh.
- Kho phan quyen vi admin, nhan vien ban hang va nhan vien giao hang co nhiem vu khac nhau.
- Kho co bang chung sau ban hang neu khong luu lich su trang thai don va thanh toan.

Minh chung:
- `app/Http/Controllers/Clients/CartController.php` kiem tra `quantity <= stock` khi them/cap nhat gio hang.
- `app/Http/Controllers/Clients/CheckoutController.php` dung transaction khi tao don, tao chi tiet don, tao thanh toan va tru ton kho.
- `app/Models/Order.php` khai bao 8 trang thai don: `pending`, `processing`, `packed`, `ready_for_delivery`, `out_for_delivery`, `delivered`, `completed`, `canceled`.
- `app/Models/Order.php` co ham `recordStatus()` luu lich su vao `order_status_history`.
- `routes/admin.php` tach quyen quan ly theo middleware: `manage_users`, `manage_products`, `manage_orders`, `manage_categories`, `manage_contacts`, `manage_deliveries`, `manage_coupons`.

## Buoc 3. Muc tieu

Muc tieu tong quat: xay dung website ban meal-kit co day du luong mua hang cho khach va luong quan tri cho nhan vien.

Muc tieu cu the:
- Khach hang xem danh sach san pham, loc theo danh muc/gia/sap xep va xem chi tiet meal-kit.
- Khach hang them san pham vao gio, cap nhat so luong, xoa san pham va dat hang.
- He thong ho tro dia chi giao hang, ma giam gia, phi giao hang va 3 cach thanh toan: tien mat, PayPal, VietQR.
- Admin quan ly danh muc, san pham, hinh anh, nguyen lieu, buoc nau, don hang, nguoi dung, ma giam gia, lien he va dashboard.
- Nhan vien giao hang xem don duoc phan cong va cap nhat tien trinh giao.
- Khach hang chi duoc danh gia san pham sau khi co don hang hoan thanh.

Tieu chi thanh cong co the kiem tra:
- Tao duoc don hang tu gio hang va co dong trong `orders`, `order_items`, `payments`.
- Khi dat hang thanh cong, ton kho san pham giam theo so luong mua.
- Moi lan cap nhat trang thai don co lich su trong `order_status_history`.
- Phan quyen admin/staff/delivery_staff/customer hoat dong theo vai tro.
- San pham meal-kit hien thi duoc nguyen lieu, buoc nau, khau phan, thoi gian nau va thong tin bao quan.

Minh chung:
- Route khach hang: `routes/web.php` co `/products`, `/cart`, `/checkout`, `/order/{id}`, `/review`, `/wishlist`, `/chat/send`.
- Route admin: `routes/admin.php` co dashboard, users, categories, products, coupons, orders, deliveries, contacts.
- Bang du lieu lien quan: `products`, `product_ingredients`, `product_cooking_steps`, `cart_items`, `orders`, `order_items`, `payments`, `shipping_addresses`, `coupons`, `reviews`.

## Buoc 4. Rang buoc

Rang buoc ky thuat:
- Du an dung Laravel 11, PHP >= 8.2, Composer va he quan tri CSDL MySQL/MariaDB.
- Mot so tinh nang phu thuoc dich vu ngoai: PayPal, VietQR, Gemini API va Python API tai `127.0.0.1:5555`.
- Neu Python API goi y/tim kiem loi, he thong van hien thi san pham theo truy van Laravel co san.
- Neu chua cau hinh Gemini API key, chatbot tra loi thong bao fallback thay vi dung ket noi AI that.

Rang buoc nghiep vu:
- Khach can dang nhap va co dia chi giao hang moi vao checkout.
- San pham khong du ton kho thi khong cho them/cap nhat gio hang vuot so luong.
- Ma giam gia phai con hieu luc, chua het han, chua vuot gioi han su dung va dung doi tuong neu la ma gan rieng.
- Nhan vien giao hang chi duoc cap nhat don duoc phan cong, tru admin.

Nhung phan khong lam trong pham vi hien tai:
- Khong co quan ly nha cung cap va phieu nhap kho.
- Khong co dinh vi giao hang thoi gian thuc.
- Khong co ung dung mobile rieng.
- Khong luu thong tin the thanh toan; PayPal/VietQR xu ly qua dich vu/thong tin cau hinh ben ngoai.

Minh chung:
- `app/Http/Controllers/Clients/CheckoutController.php` yeu cau `address_id` thuoc user hien tai va `payment_method` trong `cash`, `vietqr`; PayPal co ham rieng `placeOrderPayPal()`.
- `app/Services/VietQrService.php` chi tao QR khi da cau hinh bank bin va so tai khoan.
- `app/Http/Controllers/Clients/ChatController.php` co fallback khi khong co `GOOGLE_GEMINI_API_KEY`.
- `app/Http/Controllers/Admin/DeliveryController.php` kiem tra quyen cap nhat don qua `canHandleOrder()`.

## Buoc 5. Giai phap va cach lam

Kien truc he thong gom 4 phan:
- Client website: trang chu, san pham, chi tiet, gio hang, thanh toan, tai khoan, don hang, wishlist, review, chat.
- Admin website: dashboard, quan ly nguoi dung, danh muc, san pham, ma giam gia, don hang, giao hang, lien he.
- Co so du lieu: luu user, role, product, cart, order, payment, coupon, review, chat, notification.
- Dich vu tich hop: PayPal, VietQR, Gemini API va Python API goi y/tim kiem.

Luong xu ly dat hang:
1. Khach them san pham vao gio hang; he thong kiem tra ton kho.
2. Khach vao checkout; he thong lay dia chi mac dinh, gio hang va ma giam gia dang ap dung.
3. Khi dat hang, he thong tao `orders`, `order_items`, `payments`, tru ton kho va xoa gio hang trong mot transaction.
4. Admin xac nhan don, danh dau dang so che, da dong goi, san sang giao.
5. Nhan vien giao hang cap nhat dang giao va da giao.
6. Admin/nhan vien xac nhan thanh toan de chuyen don sang hoan thanh.
7. Khach hang co don hoan thanh moi duoc danh gia san pham.

Du lieu vao:
- Thong tin san pham meal-kit, danh muc, hinh anh, nguyen lieu, buoc nau, gia, ton kho.
- Thong tin khach hang, dia chi giao hang, gio hang, ma giam gia, phuong thuc thanh toan.

Du lieu ra:
- Don hang, chi tiet don, thanh toan, lich su trang thai, thong bao, hoa don email, danh gia, dashboard doanh thu.

Diem khac biet cua he thong:
- San pham khong chi co ten/gia/anh ma co thong tin meal-kit: khau phan, thoi gian so che/nau, calo, bao quan, han su dung, nguyen lieu va cac buoc nau.
- Quy trinh don hang co buoc dac thu cho meal-kit: dang so che, da dong goi, san sang giao.
- Ma giam gia co the gan rieng cho khach, tu dong gan khi dang ky, theo lich, hoac ap dung lam gia sale cho san pham.
- Review duoc rang buoc theo don hang da hoan thanh, tranh danh gia ao.

Minh chung:
- `app/Http/Controllers/Admin/ProductController.php` tao/cap nhat san pham, anh, nguyen lieu va buoc nau.
- `app/Services/PromotionService.php` gan coupon cho user va ap dung sale cho san pham.
- `app/Http/Controllers/Admin/OrderController.php` xu ly cac buoc xac nhan, dong goi, san sang giao, huy don, xac nhan thanh toan.
- `app/Http/Controllers/Admin/DeliveryController.php` xu ly bat dau giao va hoan tat giao.
- `app/Http/Controllers/Clients/ReviewController.php` chi cho review khi user co don `completed`.

## Buoc 6. Danh gia va kiem chung

Co the kiem chung he thong bang cac kich ban sau:

1. Kich ban gio hang:
- Them san pham con hang vao gio.
- Cap nhat so luong trong gio.
- Thu cap nhat so luong lon hon ton kho va kiem tra he thong tu choi.
- Minh chung code: `CartController::addToCart()` va `CartController::updateCart()`.

2. Kich ban dat hang:
- Dang nhap, tao dia chi giao hang, them san pham vao gio, vao checkout, ap dung coupon, dat hang bang tien mat/VietQR/PayPal.
- Kiem tra co ban ghi trong `orders`, `order_items`, `payments`; gio hang bi xoa; stock san pham bi tru.
- Minh chung code: `CheckoutController::placeOrder()` va `CheckoutController::placeOrderPayPal()`.

3. Kich ban quan ly don:
- Admin xac nhan don tu `pending` sang `processing`.
- Admin danh dau `packed`, gan nhan vien giao va chuyen sang `ready_for_delivery`.
- Nhan vien giao hang chuyen sang `out_for_delivery`, sau do `delivered`.
- Admin xac nhan thanh toan va hoan thanh don.
- Minh chung code: `OrderController`, `DeliveryController`, `Order::recordStatus()`.

4. Kich ban phan quyen:
- Tai khoan admin truy cap duoc tat ca module quan tri.
- Tai khoan staff chi vao cac module duoc gan quyen.
- Tai khoan delivery_staff duoc chuyen ve trang deliveries va chi xu ly don duoc phan cong.
- Minh chung code: `CheckPermisson.php`, `RolePermissionTableSeeder.php`, `DashboardController.php`.

5. Kich ban tuong tac khach hang:
- Khach gui lien he, chat AI, them wishlist, danh gia san pham sau khi don hoan thanh.
- Minh chung code: `ContactController`, `ChatController`, `WishListController`, `ReviewController`.

Ket qua hien co trong repo:
- Co cau truc route, controller, model, migration va view cho cac kich ban tren.
- Co seeder vai tro va danh muc meal-kit.
- Chua co test tu dong chi tiet cho tung nghiep vu; thu muc `tests` hien moi co `ExampleTest`.
- Chua co so lieu do hieu nang, so don that hoac danh gia nguoi dung that, nen khong nen ghi cac con so nhu "giam 50% thoi gian" neu chua tu do.

## Buoc 7. Dong gop va gia tri thuc

Dong gop chinh cua do an:
- Xay dung duoc prototype website ban meal-kit co luong mua hang va quan tri don hang kha day du.
- Chuan hoa du lieu meal-kit thanh cac bang rieng cho nguyen lieu va buoc nau, khong chi luu mo ta san pham dang text.
- Co quy trinh don hang phu hop voi san pham can so che/dong goi/giao hang.
- Co he thong coupon, gia sale, thong bao khuyen mai va dashboard doanh thu.
- Co chatbot ho tro tu van va API goi y/tim kiem san pham, nhung van co fallback khi dich vu ngoai loi.

Gia tri voi khach hang:
- Xem meal-kit theo danh muc, gia, danh gia, thong tin nau an va ton kho.
- Dat hang online, chon dia chi, dung ma giam gia, thanh toan bang tien mat/PayPal/VietQR.
- Theo doi don va danh gia sau khi mua that.

Gia tri voi nguoi quan tri:
- Quan ly san pham, ton kho, hinh anh, nguyen lieu, buoc nau.
- Theo doi doanh thu, san pham ban chay, don hang va trang thai giao.
- Phan cong nhan vien giao hang va luu lich su trang thai don.

Han che hien tai:
- Chua co test tu dong cho cac luong quan trong nhu checkout, coupon, giao hang, review.
- Python API goi y/tim kiem dang goi qua dia chi local `127.0.0.1:5555`, can cau hinh rieng khi trien khai.
- Chua co tracking giao hang theo ban do.
- Chua co module nhap kho, nha cung cap, loi nhuan va ke toan.
- Chua co webhook thanh toan day du de doi soat thanh toan tu ben thu ba.

Minh chung:
- `app/Http/Controllers/Admin/DashboardController.php` tong hop user, category, product, order, top san pham ban chay va doanh thu theo ngay/tuan/thang/nam.
- `app/Services/PromotionService.php` xu ly coupon tu dong va sale san pham.
- `app/Http/Controllers/Clients/SearchController.php` va `ProductController::detail()` goi Python API, co `try/catch` de khong lam hong trang khi API loi.

## Buoc 8. Tu danh gia va cam ket

Khong nen tu ghi ty le tham khao/tu lam neu chua co bang chung nhu lich su commit, tai lieu nguon, nhat ky cong viec hoac phan viec ca nhan.

Phan co the khai bao la tham khao hoac dung lai:
- Laravel framework, Eloquent ORM, Blade template.
- Thu vien xu ly anh Intervention Image.
- Tai lieu/tich hop PayPal, VietQR, Gemini API.
- Python API/NLP goi y va tim kiem neu co su dung thu vien ngoai.
- Mau giao dien admin/client neu duoc lay tu template co san.

Phan he thong hien co the hien ro trong repo:
- Thiet ke database cho ban hang meal-kit, don hang, thanh toan, coupon, giao hang, review, chat.
- Xay dung route client/admin.
- Xay dung controller xu ly gio hang, checkout, san pham, don hang, giao hang, coupon, review, chat.
- Xay dung model va quan he du lieu.
- Bo sung nghiep vu meal-kit: nguyen lieu, buoc nau, khau phan, thoi gian nau, bao quan, han su dung.

Muc do tu lam can SV xac nhan:
- Ty le tham khao: .... %
- Ty le tu lam: .... %
- Muc do tu hieu va lam chu he thong: .... %

Cam ket nen ghi:
- Chi khai bao cac chuc nang da co minh chung trong source code hoac demo.
- Khong khai bao da co so lieu hieu nang, so lieu nguoi dung that hoac ty le cai thien neu chua thuc su do.
- Neu co dung template, thu vien, API ngoai hoac ma tham khao, can ghi ro trong phu luc/tai lieu nguon.

## Tom tat ngan

Do an nay khong chi la trang hien thi san pham. He thong da bao quat quy trinh ban meal-kit tu san pham, gio hang, checkout, thanh toan, coupon, don hang, dong goi, giao hang, danh gia den dashboard quan tri.

Minh chung manh nhat nam o cac diem:
- 5 danh muc meal-kit trong `CategorySeeder`.
- Du lieu meal-kit chuan hoa trong `product_ingredients` va `product_cooking_steps`.
- 3 phuong thuc thanh toan trong `payments`: tien mat, PayPal, VietQR.
- 8 trang thai don trong `Order`.
- Phan quyen theo role/permission trong `routes/admin.php` va `CheckPermisson.php`.
- Coupon va sale tu dong trong `PromotionService`.
- Review chi sau khi mua hang hoan thanh trong `ReviewController`.
