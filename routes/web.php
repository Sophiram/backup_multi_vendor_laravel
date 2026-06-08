<?php

use App\Http\Controllers\Admin\AdminMainController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductAttributeController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductDiscountController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\MasterCategoryController;
use App\Http\Controllers\MasterSubCategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\UserMainController;
use App\Http\Controllers\Vendor\VendorAttributeController;
use App\Http\Controllers\Vendor\VendorMainController;
use App\Http\Controllers\Vendor\VendorProductController;
use App\Http\Controllers\Vendor\VendorStoreController;
use Illuminate\Support\Facades\Route;
use App\Models\AttributeValue;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\CartManagementController;
use App\Http\Controllers\Admin\CommissionController;
use App\Http\Controllers\Admin\OrderManagementController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\PaymentRequestController as AdminPaymentRequestController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\ReviewManagementController;
use App\Http\Controllers\Admin\SystemReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Vendor\ShippingCompanyController;
use App\Http\Controllers\Vendor\VendorOrderController;
use App\Http\Controllers\Vendor\VendorReportController;
use App\Http\Controllers\VendorPayoutController;
use App\Models\SubCategory;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Support\Facades\Auth;

use Livewire\Volt\Volt;


// use SimpleSoftwareIO\QrCode\Facades\QrCode;

// Route::get('/test-qr', function () {
//     return QrCode::size(200)->generate('Hello World!');
// });


Route::get('/api/attributes/{attributeId}/values', function ($attributeId) {
    return response()->json(\App\Models\AttributeValue::where('attribute_id', $attributeId)->get());
});

Route::get('/api/categories/{categoryId}/subcategories', function ($categoryId) {
    $subcategories = \App\Models\SubCategory::where('category_id', $categoryId)
        ->select('id', 'subcategory_name', 'category_id')
        ->get();
    return response()->json($subcategories, 200);
});




Route::controller(HomePageController::class)->group(function () {
    Route::get('/', 'index')->name('home');

    Route::get('/discounts',  'showDiscounts')->name('home.discounts');

    // Route សម្រាប់បង្ហាញបញ្ជី Gift Collections ទាំងអស់
    Route::get('/gift-collections',  'showGiftCollections')->name('gift.index');

// Route សម្រាប់បង្ហាញព័ត៌មានលម្អិតនៃ Gift Box នីមួយៗតាមអត្តសញ្ញាណ ID
    Route::get('/gift-collection/{id}',  'showGiftDetail')->name('gift.show');

    Route::get('/stores',  'showStores')->name('home.stores');

    Route::get('/stores/{slug}', 'storeDetails')->name('home.store.details');
    // Route::get('/category/{category_name}', 'showCategoryProducts')->name('productby.category');
    // Route::get('/products/{id}', 'productdetails')->name('product.details');

   Route::get('/about-us', 'showAboutUs')->name('about');
    Route::get('/delivery-info', 'showDeliveryInfo')->name('delivery');
    Route::get('/privacy-policy', 'showPrivacyPolicy')->name('privacy');
    Route::get('/terms-conditions', 'showTermsConditions')->name('terms');
});



Volt::route('/product/{productId}', 'product-detail-component')->name('product.details');
Volt::route('/category/{category_name}', 'product-by-category-component')->name('productby.category');
Volt::route('/wishlist', 'wishlist-page-component')->name('wishlist.index');

Volt::route('/cart', 'cart-page')->name('cart');



// Volt::route('/payment/qr/{order}', 'payment-qr-page')->name('payment.qr');

// admin routes
Route::middleware(['auth', 'verified', 'rolemanager:admin'])->group(function () {

    Route::get('/admin/pending-vendors', [AdminMainController::class, 'pendingVendors'])->name('admin.pending');
    Route::post('/admin/approve-vendor/{id}', [AdminMainController::class, 'approve'])->name('admin.approve');
    Route::get('/admin/mark-notification/{id}', function ($id) {
            $notification = auth()->user()->unreadNotifications->where('id', $id)->first();

            if ($notification) {
                $notification->markAsRead();
            }

            return redirect()->route('admin.pending');
        })->name('admin.markAsRead');

    Route::get('/admin/export-report', [AdminMainController::class, 'exportReport'])->name('admin.export.report');

    Route::get('/admin/manage/profile',  [AdminMainController::class, 'manage_profile'])->name('admin.manage.profile');
    Route::put('/admin/profile/update', [AdminMainController::class, 'update_profile'])->name('admin.profile.update');

    // Route::get('/admin/payment/add', [PaymentMethodController::class, 'add'])->name('admin.payment.add');

    // Route::post('/admin/payment/store', [PaymentMethodController::class, 'store'])->name('admin.payment.store');


    Route::get('/admin/payouts', [AdminPaymentRequestController::class, 'index'])->name('admin.payouts');
    Route::post('/admin/payouts/{id}/approve', [AdminPaymentRequestController::class, 'approve'])->name('admin.payouts.approve');
    Route::post('/admin/payouts/{id}/reject', [AdminPaymentRequestController::class, 'reject'])->name('admin.payouts.reject'); // បន្ថែមត្រង់នេះ


    Route::get('/admin/reports', [SystemReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/admin/reports/export', [SystemReportController::class, 'export'])->name('admin.reports.export');

    Route::prefix('admin/payment')->name('admin.payment.')->group(function () {
        Route::controller(PaymentMethodController::class)->group(function () {
            Route::get('/', 'index')->name('manage');
            Route::get('/add', 'add')->name('add');
            Route::post('/store', 'store')->name('store');
            Route::get('/edit/{id}', 'edit')->name('edit');
            Route::put('/update/{id}', 'update')->name('update');
            Route::delete('/delete/{id}', 'destroy')->name('delete');

            Route::post('/toggle-status/{id}',  'toggleStatus')->name('toggle.status');
        });
    });
    Route::prefix('admin')->group(function () {

            Route::get('/manage/commission', [CommissionController::class, 'index'])->name('admin.manage.commission');


            Route::controller(DiscountController::class)->group(function () {
                    Route::get('/discount/create', 'create')->name('admin.discount.create'); // បង្កើត function create() ក្នុង Controller
                    Route::post('/discount/store', 'store')->name('admin.discount.store');
                    Route::get('/discount/manage', 'index')->name('admin.discount.manage'); // នេះគឺជាកន្លែងដែលអ្នកហៅទំព័រ manage

                    Route::get('/discount/edit/{id}', 'edit')->name('admin.discount.edit');
                    Route::put('/discount/update/{id}', 'update')->name('admin.discount.update');
                    Route::delete('/discount/destroy/{id}', 'destroy')->name('admin.discount.destroy');
                });


        Route::post('/attribute/store', [AttributeController::class, 'store'])->name('admin.attribute.store');

        Route::controller(AdminMainController::class)->group(function () {
            Route::get('/dashboard', 'index')->name('admin');
           Route::get('/settings', 'setting')->name('admin.settings');
            Route::put('/settings/homepagesetting/update', 'updatehomepagesetting')->name('admin.homepagesetting.update');


           Route::get('/cart/history', 'cart_history')->name('admin.cart.history');

        //    Route::get('/order/history', 'order_history')->name('admin.order.history');
        });


        Route::controller(UserController::class)->group(function () {
           Route::get('/manage/users', 'manage_user')->name('admin.manage.users');
            Route::patch('/users/{user}/toggle-status', 'toggleStatus')->name('admin.users.toggleStatus');
            Route::post('/users/store',  'store')->name('admin.users.store');
        });


        Route::controller(VendorController::class)->group(function () {
           Route::get('/manage/stores', 'manage_store')->name('admin.manage.stores');
            Route::put('/manage/stores/{id}', 'updateStore')->name('admin.manage.stores.update');

           Route::get('/manage/vendors', 'manage_vendor')->name('admin.manage.vendors');
            Route::put('/manage/vendors/{id}', [VendorController::class, 'updateVendor'])->name('admin.manage.vendors.update');
        });
        Route::controller(OrderManagementController::class)->group(function () {

            Route::get('/order/history', 'index')->name('admin.order.history');

            Route::get('/order/show/{id}', 'show')->name('admin.order.show');

            Route::put('/order/update-status/{id}', 'updateStatus')->name('admin.order.update');

            Route::delete('/order/delete/{id}', 'destroy')->name('admin.order.delete');

            Route::get('/order/history-page', 'history')->name('admin.order.history.page');

            Route::put('/order/update-payment/{id}', 'updatePaymentStatus')->name('admin.order.payment.update');

            Route::get('/order/export', [OrderManagementController::class, 'export'])->name('admin.order.export');
        });


        Route::controller(CategoryController::class)->group(function () {
            Route::get('/category/create', 'index')->name('category.create');
           Route::get('/category/manage', 'manage')->name('category.manage');
        });

        Route::controller(SubCategoryController::class)->group(function () {
            Route::get('/subcategory/create', 'index')->name('subcategory.create');
        //    Route::get('/subcategory/manage', 'manage')->name('subcategory.manage');
        });

        Route::controller(ProductController::class)->group(function () {
            Route::get('/product/manage', 'index')->name('product.manage');


            // Route::get('/product/create', [ProductController::class, 'create'])->name('product.create');
            Route::get('/product/edit/{id}', 'edit')->name('product.edit');
             Route::put('/product/update/{id}', 'update')->name('product.update');
             Route::delete('/product/destroy/{id}', 'destroy')->name('product.destroy');

           Route::get('/product/review/manage', 'review_manage')->name('product.review.manage');

             Route::post('/admin/product/store',  'store')->name('admin.product.store');


        });

        Route::controller(ProductAttributeController::class)->group(function () {
    // ទំព័រទូទៅ
            Route::get('/productattribute/create', 'index')->name('productattribute.create');
            Route::get('/productattribute/list', 'index')->name('attribute.list');
            Route::get('/productattribute/manage', 'manage')->name('productattribute.manage');

            // ការគ្រប់គ្រង ProductAttribute (ប្រើដដែល)
            // Route::get('/productattribute/create', 'index')->name('attribute.create.form'); // សម្រាប់បង្ហាញទំព័រ
            Route::post('/defaultattribute/create', 'createattribute')->name('attribute.create');
            Route::get('/defaultattribute/edit/{id}', 'showattribute')->name('show.attribute');
            Route::put('/defaultattribute/update/{id}', 'updateattribute')->name('update.attribute');
            Route::delete('/defaultattribute/delete/{id}', 'deleteattribute')->name('delete.attribute');

            // ការគ្រប់គ្រង Global Attribute (បន្ថែមថ្មី)
            // ត្រូវប្រាកដថាប្រើឈ្មោះ Route នេះឱ្យត្រូវនឹងអ្វីដែលអ្នកសរសេរក្នុង Blade
            Route::get('/attribute/edit/{id}', 'editGlobal')->name('admin.attribute.edit');
            Route::delete('/attribute/destroy/{id}', 'destroyGlobal')->name('admin.attribute.destroy');
            Route::put('/attribute/update/{id}', 'updateGlobal')->name('admin.attribute.update');
        });

        // Route::controller(ProductDiscountController::class)->group(function () {
        //     Route::get('/discount/create', 'index')->name('discount.create');
        //    Route::get('/discount/manage', 'manage')->name('discount.manage');
        // });


        Route::controller(ReviewManagementController::class)->group(function () {

            Route::get('/product/review/manage', 'manageReview')->name('admin.reviews.manage');
            Route::put('/reviews/{id}', 'update')->name('admin.reviews.update');
            Route::delete('/reviews/reject/{id}', 'reject')->name('admin.review.reject');
        });

        Route::controller(MasterCategoryController::class)->group(function () {
            Route::post('/store/category', 'storecategory')->name('store.category');
            Route::get('/category/{id}', 'showcat')->name('show.cat');
            Route::put('/category/update{id}', 'updatecat')->name('update.cat');
            Route::delete('/category/delete{id}', 'deletecat')->name('delete.cat');
        });

        Route::controller(MasterSubCategoryController::class)->group(function () {
            Route::get('/subcategory/manage', 'manage')->name('subcategory.manage');
            Route::post('/store/subcategory', 'storesubcategory')->name('store.subcategory');
            Route::get('/subcategory/{id}', 'showsubcat')->name('show.subcat');
            Route::put('/subcategory/update/{id}', 'updatesubcat')->name('update.subcat');
            Route::delete('/subcategory/delete/{id}', 'deletesubcat')->name('delete.subcat');
            // Route::get('/manage/subcategory', [MasterSubCategoryController::class, 'manage'])->name('manage.subcategory');

        });


        Route::controller(CartManagementController::class)->group(function () {
            Route::get('/cart/history', 'index')->name('admin.cart.history');
            Route::get('/cart/show/{id}', 'show')->name('admin.cart.show');
            Route::get('/cart/export', 'export')->name('admin.cart.export');

            Route::get('/cart/edit/{id}', 'edit')->name('admin.cart.edit');
    Route::put('/cart/update/{id}', 'update')->name('admin.cart.update');

            Route::delete('/cart/delete/{id}', [CartManagementController::class, 'destroy'])->name('admin.cart.delete');
        });

    });
});



// vendor routes

Route::middleware(['auth', 'verified', 'rolemanager:vendor', 'approved.vendor'])->group(function () {
    Route::prefix('vendor')->group(function () {

        Route::get('/order/history', [VendorOrderController::class, 'vendorIndex'])->name('vendor.orders.history');
        // បន្ថែម Route នេះទៅក្នុងក្រុមរបស់ Vendor (prefix 'vendor')
        Route::get('/order/show/{id}', [VendorOrderController::class, 'vendorShowOrder'])->name('vendor.ordershow');


        Route::post('/orders/{order}/update-status', [VendorOrderController::class, 'updateStatus'])
            ->name('vendor.orders.updateStatus');



        Route::get('/products/{id}', [ProductController::class, 'show'])->name('vendor.products.show');
    // Route::get('/order/export', [OrderController::class, 'export'])->name('order.export');

        Route::get('/payout', [VendorPayoutController::class, 'index'])->name('vendor.payout.index');



        // Shipping Routes
    Route::get('/shipping', [ShippingCompanyController::class, 'index'])->name('vendor.shipping.index');
    Route::post('/shipping/store', [ShippingCompanyController::class, 'store'])->name('vendor.shipping.store');
    Route::delete('/shipping/{id}', [ShippingCompanyController::class, 'destroy'])->name('vendor.shipping.destroy');
    Route::put('/shipping/{id}', [ShippingCompanyController::class, 'update'])->name('shipping.update');


    Route::post('/payout/request', [VendorPayoutController::class, 'requestPayout'])->name('vendor.payout.request');
        Route::controller(VendorMainController::class)->group(function () {

            Route::get('/dashboard', 'index')->name('vendor');
            Route::get('/profile', 'profile')->name('vendor.profile');
            Route::get('/settings', 'settings')->name('vendor.settings');
            Route::patch('/profile', 'update')->name('vendor.profile.update');
            Route::get('/sales/report', 'salesReport')->name('vendor.sales.report');
            Route::get('/vendor/sales-report/export', 'exportSalesReport')->name('vendor.report.export');
        });

        Route::controller(VendorProductController::class)->group(function () {
            Route::get('/product/create', 'index')->name('vendor.product.create');
            Route::get('/product/manage', 'manage')->name('vendor.product.manage');
            Route::post('/product/store', 'storeproduct')->name('vendor.product.store');

            Route::get('/product/{id}', 'showproduct')->name('show.product');
            Route::put('/product/update/{id}', 'updateproduct')->name('update.product');
            Route::delete('/product/delete/{id}', 'deleteproduct')->name('delete.product');
        });

        Route::controller(VendorStoreController::class)->group(function () {
            Route::get('/store/create', 'index')->name('vendor.store.create');
            Route::get('/store/manage', 'manage')->name('vendor.store.manage');
            Route::post('/store/publish', 'store')->name('create.store');

            Route::get('/store/{id}', 'showstore')->name('show.store');
            Route::put('/store/update/{id}', 'updatestore')->name('update.store');
            Route::delete('/store/delete/{id}', 'deletestore')->name('delete.store');
        });

        Route::controller(VendorAttributeController::class)->group(function () {
            Route::get('/attribute/create', 'index')->name('vendor.attribute.create');
            Route::get('/attribute/manage', 'manage')->name('vendor.attribute.manage');
            Route::post('/attribute/store', 'store')->name('vendor.attribute.store');

            // Route::get('/attribute/{id}', 'show')->name('vendor.show.attribute');
            Route::put('/attribute/update/{id}', 'update')->name('vendor.update.attribute');
            Route::delete('//attribute/delete/{id}', 'delete')->name('vendor.delete.attribute');
        });
    });
});


// user routes
Route::middleware(['auth', 'verified', 'rolemanager:user'])->group(function () {
    Route::prefix('user')->group(function () {

        // Volt::route('/payment/qr/{order}', 'payment.qr')->name('payment.qr');

        Route::controller(UserMainController::class)->group(function () {
            Route::get('/dashboard', 'index')->name('dashboard');
            Route::get('/order/history', 'history')->name('user.order.history');

            Route::get('/orders/{order}', 'show')->name('user.order.show');



            Route::get('/settings/payment', 'payment')->name('user.payment');
            Route::get('/affiliate', 'affiliate')->name('user.affiliate');

             Route::get('/profile',  'profile')->name('user.profile');
            Route::patch('/profile', 'update')->name('user.profile.update');
             Route::delete('/profile',  'destroy')->name('user.profile.destroy');

             Route::get('/notifications/read-all', function () {
                    Auth::user()->unreadNotifications->markAsRead();
                    return redirect()->back();
            })->name('user.notifications.readAll');


            Route::get('/profile/password', [ProfileController::class, 'editPassword'])->name('user.password.edit');
            Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('user.password.update');
        });
    });
});




Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::post('/product/review', [\App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store')->middleware('auth');

    Volt::route('/checkout', 'checkout-page')->name('checkout');

// Receipt Route
    Volt::route('/payment/qr/{order}', 'payment-qr-page')->name('payment.qr');
    Volt::route('/receipt/{order}', 'receipt-component')->name('receipt');

});

// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::get('/dashboard', [UserController::class, 'UserDashboard'])->name('dashboard');
//     Route::get('/profile', [UserController::class, 'UserProfile'])->name('user.profile');
// });


require __DIR__.'/auth.php';
