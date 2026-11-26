<?php

use App\Http\Controllers\CheckPaymentController;
use App\Http\Controllers\OrderReceiptController;
use App\Livewire\Bank;
use App\Livewire\Optimize;
use App\Livewire\ProductDetail1;
use App\Livewire\Shop;
use App\Livewire\Thankyou;
use App\Http\Middleware\CheckLogin;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Livewire\Cart;
use App\Livewire\Checkout;
use App\Livewire\ProductDetail;
use App\Livewire\Home;
use App\Livewire\About;
use App\Livewire\Contact;
use App\Livewire\ShippingPolicy;
use App\Livewire\PrivacyPolicy;
use App\Livewire\PaymentPolicy;
use App\Livewire\Transactions;
use App\Livewire\TransactionsList;
use App\Livewire\WarrantyPolicy;
use App\Livewire\ReturnPolicy;
use App\Livewire\OrderTracking;
use App\Livewire\Blog;
use App\Livewire\Search;
use App\Livewire\BlogPost;
use App\Livewire\Account;
use App\Livewire\AccountOrders;
use App\Livewire\ChangePassword;
use App\Livewire\AccountAddresses;
use App\Livewire\AccountOrdersDetail;
use App\Livewire\Login;
use App\Livewire\Logout;
use App\Livewire\Register;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/bao-tri', function () {
    return 'Bảo trì';
});

Route::get('/', Home::class);
Route::get('/black-friday', \App\Livewire\PromotionCampaignPage::class)->name('promotions.black-friday');
Route::get('/khuyen-mai/{slug}', \App\Livewire\PromotionCampaignPage::class)->name('promotions.show');
Route::get('/about', About::class);
Route::get('/contact', Contact::class);
Route::get('/sua-chua-gear', \App\Livewire\RepairServices::class)->name('repair-services');
Route::get('/chinh-sach-van-chuyen', ShippingPolicy::class);
Route::get('/chinh-sach-bao-mat', PrivacyPolicy::class);
Route::get('/chinh-sach-thanh-toan', PaymentPolicy::class);
Route::get('/chinh-sach-bao-hanh', WarrantyPolicy::class);
Route::get('/chinh-sach-doi-tra-hang-hoan-tien', ReturnPolicy::class);
Route::get('/orders', \App\Livewire\WarrantyLookup::class)->name('orders.tracking');
Route::get('/tra-cuu-don-hang', \App\Livewire\WarrantyLookup::class);
Route::get('/tra-cuu-bao-hanh', \App\Livewire\WarrantyLookup::class)->name('warranty.lookup');
Route::get('/kich-hoat', \App\Livewire\WarrantyLookup::class)->name('warranty.activate');
Route::get('/kich-hoat/{code}', \App\Livewire\WarrantyLookup::class)->name('warranty.activate.code');
Route::get('/products', Shop::class);
Route::get('/products/{slug}', ProductDetail::class);
Route::redirect('/guides', '/blogs');
Route::redirect('/login', '/account/login');
Route::get('/blogs', Blog::class);
Route::get('/test-thiet-bi', \App\Livewire\DeviceTester::class)->name('device-tester');
Route::get('/blog/{slug}', BlogPost::class);
Route::get('/search', Search::class);

// Account routes

Route::middleware([CheckLogin::class])->group(function () {
    Route::get('/account', Account::class);
    Route::get('/account/orders', AccountOrders::class);
    Route::get('/account/orders/{orderCode}', AccountOrdersDetail::class);
    Route::get('/account/order-receipts/{order}', OrderReceiptController::class)->name('orders.receipt');
    Route::get('/account/changepassword', ChangePassword::class);
    // Route::get('/account/addresses', AccountAddresses::class);
    Route::get('/account/logout', Logout::class);
    Route::get('/checkout', Checkout::class);
    Route::get('/thanks/{orderCode}', Thankyou::class)->name('thanks');
    Route::get('/bank/{orderCode}', Bank::class)->name('bank');
});




Route::get('/shop', Shop::class);
Route::get('/cart', Cart::class);
Route::get('/product/{slug}', ProductDetail::class);
Route::get('/product1/{slug}', fn (string $slug) => redirect('/product/'.$slug, 301));
Route::get('/sitemap-products.xml', \App\Http\Controllers\ProductSitemapController::class);



Route::middleware(RedirectIfAuthenticated::class)->group(function () {
    Route::get('/auth/google', [Login::class, 'redirectToProvider'])->name('google.login');
    Route::get('/auth/google/register', [Register::class, 'redirectToProvider'])->name('google.register');
    Route::get('/auth/google/callback', [Login::class, 'handleGoogleCallback']);
    Route::get('/account/login', Login::class);
    Route::get('/account/register', Register::class);
});
Route::get('/checkpayment/{orderCode}', [CheckPaymentController::class, 'checkPayment']);
Route::get('/transactions', Transactions::class);
// Route::get('/transactions-list', TransactionsList::class)->name('transactions.list');

Route::get('/optimize', Optimize::class);
