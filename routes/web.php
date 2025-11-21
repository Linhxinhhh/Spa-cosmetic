<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthenticatedSessionController as AdminAuth;
use App\Http\Controllers\Admin\{
    AdminController,
    AnalyticsController,
    AppointmentController, BannerController, BrandController, ServiceController,
    ProductController,ProductImageController, UserRoleController, CustomerController,
    ProductCategoryController, ServiceCategoryController, SearchController, GuideController,
    GuideCategoryController, GuideTagController,ContactAdminController,FaqAdminController, TreatmentPlanController
};
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\{
    OrderUserController,
    UserAuthController,
    CategoryApiController,
    ProductUserController,
    BookingController,
    ServiceUserController
    ,CartController,CompareController,WishlistController
    ,CheckoutController,PaymentController,PriceListController,NewsletterController,ContactController, FaqController, ChatbotController, AboutController, CustomerTreatmentController, CustomerSessionController
};
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\GuideController as AdminGuide;
use App\Http\Controllers\User\GuideUserController as FrontGuide;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
Route::get('/gemini/models', function () {
    $key = env('GEMINI_API_KEY');
    return Http::get("https://generativelanguage.googleapis.com/v1/models?key=$key")->json();
});
// trang dashboard admin
Route::prefix('admin')->name('admin.')->group(function () {
    // Khách (chưa đăng nhập admin)
    Route::middleware('guest:admin')->group(function () {
        Route::get('register', [AdminAuth::class, 'registerForm'])->name('register');
        Route::post('register', [AdminAuth::class, 'register'])->name('register.store');
        Route::get('login',  [AdminAuth::class, 'create'])->name('login');
        Route::post('login', [AdminAuth::class, 'store'])->name('login.post');
    });
   
       
    // Đã đăng nhập admin + đúng quyền
    
    Route::middleware('auth:admin')->group(function () {
     Route::post('logout', [AdminAuth::class, 'destroy'])->name('logout');
        // Dashboard quản trị
        Route::get('dashboard', [AdminController::class, 'index'])->name('dashboard');


        /* ===== TẤT CẢ ROUTE QUẢN TRỊ BÊN DƯỚI ĐỀU NẰM TRONG NHÓM NÀY ===== */
        Route::get('/profile',       [ProfileController::class, 'index'])->name('profile.index');
        Route::get('/profile/edit',  [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile',     [ProfileController::class, 'update'])->name('profile.update');
        // Phân tích & Báo cáo
        Route::get('analytics', [AnalyticsController::class, 'index'])
        ->name('analytics.index');

        Route::get('analytics/summary', [AnalyticsController::class, 'summary'])
        ->name('analytics.summary'); // trả JSON KPI

        Route::get('analytics/timeseries', [AnalyticsController::class, 'timeseries'])
        ->name('analytics.timeseries'); // trả JSON chuỗi thời gian

        Route::get('analytics/top', [AnalyticsController::class, 'top'])
        ->name('analytics.top'); // top sản phẩm/dịch vụ/khách
        Route::get('analytics/order-status', [AnalyticsController::class, 'orderStatus'])
        ->name('analytics.orderStatus');
        
        // Tìm kiếm
        Route::get('product-categories/products/search', [SearchController::class, 'index'])->name('categories.products.search');
        Route::get('service-categories/services/search', [SearchController::class, 'search'])->name('service_categories.search');
        Route::get('products/search',  [SearchController::class, 'products'])->name('products.search');
        Route::get('services/search',  [SearchController::class, 'services'])->name('services.search');
        Route::get('brands/search',    [SearchController::class, 'brands'])->name('brands.search');
        Route::get('banners/search',   [SearchController::class, 'banners'])->name('banners.search');

        // Danh mục SP
        Route::get('product-categories',               [ProductCategoryController::class, 'index'])->name('product_categories.index');
        Route::get('product-categories/create',        [ProductCategoryController::class, 'create'])->name('product_categories.create')->middleware('checkrole:admin,create');
        Route::post('product-categories',              [ProductCategoryController::class, 'store'])->name('product_categories.store')->middleware('checkrole:admin,store');
        Route::get('product-categories/{id}/edit',     [ProductCategoryController::class, 'edit'])->name('product_categories.edit')->middleware('checkrole:admin,edit');
        Route::put('product-categories/{id}',          [ProductCategoryController::class, 'update'])->name('product_categories.update')->middleware('checkrole:admin,update');
        Route::delete('product-categories/{id}',       [ProductCategoryController::class, 'destroy'])->name('product_categories.destroy')->middleware('checkrole:admin,destroy');

        // Danh mục DV
        Route::get('service-categories',               [ServiceCategoryController::class, 'index'])->name('service_categories.index');
        Route::get('service-categories/create',        [ServiceCategoryController::class, 'create'])->name('service_categories.create')->middleware('checkrole:admin,create');
        Route::post('service-categories',              [ServiceCategoryController::class, 'store'])->name('service_categories.store')->middleware('checkrole:admin,store');
        Route::get('service-categories/{id}/edit',     [ServiceCategoryController::class, 'edit'])->name('service_categories.edit')->middleware('checkrole:admin,edit');
        Route::put('service-categories/{id}',          [ServiceCategoryController::class, 'update'])->name('service_categories.update')->middleware('checkrole:admin,update');
        Route::delete('service-categories/{id}',       [ServiceCategoryController::class, 'destroy'])->name('service_categories.destroy')->middleware('checkrole:admin,destroy');

        // Sản phẩm
        Route::get('products',               [ProductController::class, 'index'])->name('products.index');
        Route::get('products/create',        [ProductController::class, 'create'])->name('products.create')->middleware('checkrole:admin,create');
        Route::post('products',              [ProductController::class, 'store'])->name('products.store')->middleware('checkrole:admin,store');
        Route::get('products/{id}/edit',     [ProductController::class, 'edit'])->name('products.edit')->middleware('checkrole:admin,edit');
        Route::put('products/{id}',          [ProductController::class, 'update'])->name('products.update')->middleware('checkrole:admin,update');
        Route::delete('products/{id}',       [ProductController::class, 'destroy'])->name('products.destroy')->middleware('checkrole:admin,destroy');
         Route::post('/products/{product}/images',               [ProductImageController::class, 'store'])->name('products.images.store');
        Route::delete('/products/{product}/images/{image}',     [ProductImageController::class, 'destroy'])->name('products.images.destroy');
        Route::post('/products/{product}/images/sort',          [ProductImageController::class, 'sort'])->name('products.images.sort');
        Route::post('/products/{product}/images/{image}/main',  [ProductImageController::class, 'setMain'])->name('products.images.setMain');
            // Dịch vụ
        Route::get('services',               [ServiceController::class, 'index'])->name('services.index');
        Route::get('services/create',        [ServiceController::class, 'create'])->name('services.create')->middleware('checkrole:admin,create');
        Route::post('services',              [ServiceController::class, 'store'])->name('services.store')->middleware('checkrole:admin,store');
        Route::get('services/{id}/edit',     [ServiceController::class, 'edit'])->name('services.edit')->middleware('checkrole:admin,edit');
        Route::put('services/{id}',          [ServiceController::class, 'update'])->name('services.update')->middleware('checkrole:admin,update');
        Route::delete('services/{id}',       [ServiceController::class, 'destroy'])->name('services.destroy')->middleware('checkrole:admin,destroy');

        // Thương hiệu
        Route::get('brands',                 [BrandController::class, 'index'])->name('brands.index');
        Route::get('brands/create',          [BrandController::class, 'create'])->name('brands.create')->middleware('checkrole:admin,create');
        Route::post('brands',                [BrandController::class, 'store'])->name('brands.store')->middleware('checkrole:admin,store');
        Route::get('brands/{id}/edit',       [BrandController::class, 'edit'])->name('brands.edit')->middleware('checkrole:admin,edit');
        Route::put('brands/{id}',            [BrandController::class, 'update'])->name('brands.update')->middleware('checkrole:admin,update');
        Route::delete('brands/{id}',         [BrandController::class, 'destroy'])->name('brands.destroy')->middleware('checkrole:admin,destroy');

        // Lịch hẹn
        Route::get('appointments',           [AppointmentController::class, 'index'])->name('appointments.index');
        Route::post('appointments',          [AppointmentController::class, 'store'])->name('appointments.store')->middleware('checkrole:admin,store');
        Route::get('appointments/{id}/edit', [AppointmentController::class, 'edit'])->name('appointments.edit')->middleware('checkrole:admin,edit');
        Route::put('appointments/{id}',      [AppointmentController::class, 'update'])->name('appointments.update')->middleware('checkrole:admin,update');
        Route::delete('appointments/{id}',   [AppointmentController::class, 'destroy'])->name('appointments.destroy')->middleware('checkrole:admin,destroy');

        // Khách hàng
        Route::get('customers',              [CustomerController::class, 'index'])->name('customers.index');
        Route::post('customers',             [CustomerController::class, 'store'])->name('customers.store')->middleware('checkrole:admin,store');
        Route::get('customers/{customer}/edit',[CustomerController::class, 'edit'])->name('customers.edit')->middleware('checkrole:admin,edit');
        Route::put('customers/{customer}',   [CustomerController::class, 'update'])->name('customers.update')->middleware('checkrole:admin,update');
        Route::delete('customers/{customer}',[CustomerController::class, 'destroy'])->name('customers.destroy')->middleware('checkrole:admin,destroy');

        //lich trinh cho dich vu 
        Route::get('/treatment-plans', [TreatmentPlanController::class, 'index'])->name('treatment-plans.index');
        Route::get('/treatment-plans/create', [TreatmentPlanController::class, 'create'])->name('treatment-plans.create');
        Route::post('/treatment-plans/preview', [TreatmentPlanController::class, 'preview'])->name('treatment-plans.preview');
        Route::post('/treatment-plans', [TreatmentPlanController::class, 'store'])->name('treatment-plans.store');
        Route::get('/treatment-plans/{plan}', [TreatmentPlanController::class, 'show'])->name('treatment-plans.show');
         Route::get('treatment-plans/{plan}/edit', [TreatmentPlanController::class, 'edit'])->name('treatment-plans.edit');
        Route::put('treatment-plans/{plan}', [TreatmentPlanController::class, 'update'])->name('treatment-plans.update');

            // buổi
        Route::get('treatment-sessions', [\App\Http\Controllers\Admin\TreatmentSessionController::class, 'index'])
        ->name('tsessions.index');
        Route::get('/treatment-sessions/{session}/edit', [\App\Http\Controllers\Admin\TreatmentSessionController::class, 'edit'])->name('tsessions.edit');
        Route::put('/treatment-sessions/{session}', [\App\Http\Controllers\Admin\TreatmentSessionController::class, 'update'])->name('tsessions.update');
        Route::delete('/treatment-sessions/{session}', [\App\Http\Controllers\Admin\TreatmentSessionController::class, 'destroy'])->name('tsessions.destroy');
        // Phân quyền
        Route::get('user-roles',             [UserRoleController::class,'index'])->name('user_roles.index');
        Route::get('user-roles/{user}/edit', [UserRoleController::class,'edit'])->name('user_roles.edit')->middleware('checkrole:admin,edit');
        Route::put('user-roles/{user}',      [UserRoleController::class,'update'])->name('user_roles.update')->middleware('checkrole:admin,update');
        // Đơn hàng
        Route::get('/orders',            [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}',     [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
        Route::put('/orders/{order}/status',  [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('status');
        Route::put('/orders/{order}/payment', [\App\Http\Controllers\Admin\OrderController::class, 'updatePayment'])->name('payment');
        // Banner
        Route::get('banners',                [BannerController::class, 'index'])->name('banners.index');
        Route::get('banners/create',         [BannerController::class, 'create'])->name('banners.create')->middleware('checkrole:admin,create');
        Route::post('banners',               [BannerController::class, 'store'])->name('banners.store')->middleware('checkrole:admin,store');
        Route::get('banners/{banner}/edit',  [BannerController::class, 'edit'])->name('banners.edit')->middleware('checkrole:admin,edit');
        Route::put('banners/{banner}',       [BannerController::class, 'update'])->name('banners.update')->middleware('checkrole:admin,update');
        Route::delete('banners/{banner}',    [BannerController::class, 'destroy'])->name('banners.destroy')->middleware('checkrole:admin,destroy');
        //quan ly bai viet
    Route::resource('guides', AdminGuide::class)
        ->scoped(['guide' => 'guide_id'])   // <— QUAN TRỌNG
        ->except(['show']);

    // Bật/tắt xuất bản (sau khi scoped, {guide} cũng là guide_id)
    Route::patch('guides/{guide}/toggle', [AdminGuide::class, 'togglePublish'])
        ->name('guides.togglePublish');
        //quan ly contact và phản hồi
         Route::get   ('/contacts',                 [ContactAdminController::class, 'index'])->name('contacts.index');
        Route::get   ('/contacts/{contact}',       [ContactAdminController::class, 'show'])->name('contacts.show');
        Route::patch ('/contacts/{contact}/status',[ContactAdminController::class, 'updateStatus'])->name('contacts.status');
        Route::post  ('/contacts/{contact}/reply', [ContactAdminController::class, 'reply'])->name('contacts.reply');
        Route::delete('/contacts/{contact}',       [ContactAdminController::class, 'destroy'])->name('contacts.destroy');
        // Hỏi đáp
 
        Route::resource('/faqs', FaqAdminController::class)
            ->parameters(['faqs' => 'faq'])
            ->names('faqs'); // -> admin.faqs.*

        Route::patch('/faqs/{faq}/toggle', [FaqAdminController::class, 'toggle'])
            ->name('faqs.toggle');
    });
      });
    //nguoidung
Route::prefix('users')->name('users.')->group(function () {
    Route::middleware('auth')->group(function () {
        // Trang nhắc xác minh
        Route::get('/email/verify', function () {
            return view('auth.verify-email'); // tạo view này
        })->name('verification.notice');

        // Link xác minh trong email
        Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
            $request->fulfill(); // đánh dấu email đã xác minh
            return redirect()->route('users.home')->with('success', 'Xác minh email thành công!');
        })->middleware(['signed'])->name('verification.verify');

        // Gửi lại email xác minh
        Route::post('/email/verification-notification', function (Request $request) {
            $request->user()->sendEmailVerificationNotification();
            return back()->with('status', 'Đã gửi lại email xác minh!');
        })->middleware(['throttle:6,1'])->name('verification.send');
    });
    // Trang chủ, danh mục, tìm kiếm, chi tiết
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/products', [ProductUserController::class, 'index'])->name('products.index');
    Route::get('/products/search', [ProductUserController::class, 'search'])->name('products.search');
    Route::get('/products/{product:slug}', [ProductUserController::class, 'show'])->name('products.show');
    Route::get('/category/{category:slug}', [ProductUserController::class, 'byCategory'])->name('products.byCategory');
Route::get('/products/brand/{brand:slug}', [ProductUserController::class, 'byBrand'])
    ->name('products.byBrand');
    // API danh mục (public)
    Route::get('/{type}-categories/parents', [CategoryApiController::class, 'parents'])
        ->where('type','product|service');
    Route::get('/{type}-categories/{id}/children', [CategoryApiController::class, 'children'])
        ->where('type','product|service');

// PUBLIC (không gắn auth)
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product:slug}', [CartController::class, 'add'])
    ->name('cart.add');
    Route::post('/cart/add/id/{product:product_id}', [CartController::class, 'add'])
    ->name('cart.addById');
// Dùng {item} là số (id cart_item khi user) hoặc product_id (guest)
Route::patch('/cart/update/{item}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{item}', [CartController::class, 'remove'])->name('cart.remove');
    
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    // Compare (cho phép KHÁCH dùng session)
    Route::get('/compare', [CompareController::class, 'index'])->name('compare.index');
    Route::post('/compare/add/{product}', [CompareController::class, 'add'])->name('compare.add');        // session
    Route::delete('/compare/remove/{product}', [CompareController::class, 'remove'])->name('compare.remove'); // session

    // Wishlist: tùy bạn — thường yêu cầu đăng nhập (gắn với user). Nếu muốn cho khách tạm lưu session thì cũng để public.
    // Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    // Route::post('/wishlist/{product}/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Dịch vụ, đặt lịch (xem public)
// Trang danh sách dịch vụ (index)
    Route::get('/dich-vu', [ServiceUserController::class, 'index'])->name('services.index');
Route::get('/dich-vu/danh-muc/{category:slug}', [ServiceUserController::class, 'byCategory'])
    ->name('services.byCategory');
    Route::get('/dich-vu/bang-gia', [PriceListController::class, 'index'])
    ->name('pricelist.index');
// Trang chi tiết dịch vụ (show)
    Route::get('/orders', [OrderUserController::class, 'index'])
        ->name('orders.index');

    // Chi tiết đơn hàng
    Route::get('/orders/{order}', [OrderUserController::class, 'show'])
        ->name('orders.show')
        ->whereNumber('order');

    // FAG
    Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');
    // ===== AUTH ONLY (bắt buộc đăng nhập) =====
    Route::middleware('auth')->group(function () {
        Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
        Route::post('/checkout/pay', [CheckoutController::class, 'pay'])->name('checkout.pay');

        // chặn GET nhầm vào pay
        Route::get('/checkout/pay', fn () =>
            redirect()->route('users.checkout.show')
                ->with('error','Phải bấm nút Thanh toán (POST), không mở URL trực tiếp.')
        );
        Route::get('/dich-vu/{service:slug}', [ServiceUserController::class, 'show'])->name('services.show');

        Route::get('/dat-lich/{service:slug?}', [BookingController::class, 'create'])->name('booking.create');

 
        Route::post('/dat-lich', [BookingController::class, 'store'])->name('booking.store');
           Route::get('/my-appointments',
        [BookingController::class, 'index'])
        ->name('booking.index');
         Route::get('/my-appointments/{id}',
        [BookingController::class, 'showAppointment'])
        ->name('booking.show');
        
         Route::post('/my-appointments/{id}/reschedule',
    [BookingController::class, 'rescheduleAppointment'])
        ->name('booking.reschedule');

        

        
        // Các trang tài khoản, 
        // lịch sử lich hen 
        Route::get('/my-treatments', [CustomerTreatmentController::class, 'index'])->name('treatments.index');
        Route::get('/my-treatments/{plan}', [CustomerTreatmentController::class, 'show'])->name('treatments.show');
        Route::get('/my-sessions', [CustomerTreatmentController::class, 'sessions'])->name('sessions.index');
    });
    
    // ===== AUTH (login/register) — dành cho khách =====
    Route::middleware('guest')->group(function () {
        Route::get('/login', [UserAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [UserAuthController::class, 'login'])->name('login.post')->middleware('throttle:6,1');
        Route::get('/register', [UserAuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [UserAuthController::class, 'register'])->name('register.post');
        Route::get('/forgot-password', [UserAuthController::class, 'showForgot'])->name('password.request');
        Route::post('/forgot-password', [UserAuthController::class, 'sendReset'])->name('password.email');
        Route::get('/reset-password/{token}', [UserAuthController::class, 'showReset'])->name('password.reset');
        Route::post('/reset-password', [UserAuthController::class, 'reset'])->name('password.update');
    });
    Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])
    ->name('newsletter.subscribe');

    // Logout
    Route::post('/logout', [UserAuthController::class, 'logout'])->middleware('auth')->name('logout');

    // Payment return (public) + IPN (bỏ CSRF, nên dùng GET cho VNPAY, POST cho MoMo)
    Route::get('/payment/vnpay/return',  [\App\Http\Controllers\User\PaymentController::class, 'vnpayReturn'])->name('payment.vnpay.return');
    Route::get('/payment/vnpay/ipn',     [\App\Http\Controllers\User\PaymentController::class, 'vnpayIpn'])->name('payment.vnpay.ipn'); // VNPAY thường GET
    Route::get('/payment/momo/return',   [\App\Http\Controllers\User\PaymentController::class, 'momoReturn'])->name('payment.momo.return');
    Route::post('/payment/momo/ipn',     [\App\Http\Controllers\User\PaymentController::class, 'momoIpn'])->name('payment.momo.ipn');
    // Cẩm nang
    Route::get('/cam-nang',       [FrontGuide::class,'index'])->name('guides.index');
    Route::get('cam-nang/{guide:slug}', [FrontGuide::class,'show'])->name('guides.show');
    // Liên hệ
    Route::get('/lien-he', [ContactController::class, 'index'])
    ->name('contact.index');

    Route::post('/lien-he', [ContactController::class, 'submit'])
        ->name('contact.submit');
        //Gioi thieu
Route::get('/gioi-thieu', [AboutController::class, 'index'])
    ->name('about.index');
    // Chatbot
    Route::post('/chat/stream', [ChatbotController::class, 'stream'])->name('chat.stream');

    Route::get('/chat', [ChatbotController::class,'index'])->name('chat.index');
    Route::post('/chat/send', [ChatbotController::class,'send'])->name('chat.send');
    // xem/doi ngay gio/huy lich trình dịch vụ 
     Route::get('/my-sessions', [CustomerSessionController::class, 'index'])
        ->name('customer.sessions.index');

    // form dời lịch (chọn giờ mới)
    Route::get('/my-sessions/{session}/edit', [CustomerSessionController::class, 'edit'])
        ->name('customer.sessions.edit');

    // lưu giờ mới
    Route::put('/my-sessions/{session}', [CustomerSessionController::class, 'update'])
        ->name('customer.sessions.update');

    // hủy buổi
    Route::post('/my-sessions/{session}/cancel', [CustomerSessionController::class, 'cancel'])
        ->name('customer.sessions.cancel');
});

// đổi chi nhánh (lưu session và redirect back)
Route::get('/branch/set', function(\Illuminate\Http\Request $r){
    session(['branch' => $r->get('branch')]);
    return back();
})->name('users.branch.set');

Route::get('/api/availability', [BookingController::class, 'availability'])->name('api.availability');



Route::get('/', function () {
    return 'Laravel is running';
});