<?php

Route::get('/', 'PagesController@index')->name('pages.index');
Route::get('products', 'ProductsController@index')->name('products.index');
Route::get('products/{product}/show', 'ProductsController@show')->name('products.show');
Route::get('products/{product}/detail', 'ProductsController@detail')->name('products.detail');


Auth::routes();

Route::group(['middleware' => 'auth'], function () {

    /*邮箱验证*/
    Route::get('/email_verification/send', 'EmailVerificationController@send')->name('email_verification.send');
    Route::get('/email_verify_notice', 'PagesController@emailVerifyNotice')->name('email_verify_notice');
    Route::get('/email_verification/verify', 'EmailVerificationController@verify')->name('email_verification.verify');

    Route::group(['middleware' => 'email_verified'], function () {

        /*用户地址*/
        Route::get('user_addresses', 'UserAddressesController@index')->name('user_addresses.index');
        Route::get('user_addresses/create', 'UserAddressesController@create')->name('user_addresses.create');
        Route::post('user_addresses', 'UserAddressesController@store')->name('user_addresses.store');
        Route::get('user_addresses/{user_address}', 'UserAddressesController@edit')->name('user_addresses.edit');
        Route::put('user_addresses/{user_address}', 'UserAddressesController@update')->name('user_addresses.update');
        Route::delete('user_addresses/{user_address}', 'UserAddressesController@destory')->name('user_addresses.destory');

        /*商品*/
//        Route::get('products', 'ProductsController@index')->name('products.index');
//        Route::get('products/{product}', 'ProductsController@show')->name('products.show');

        /*购物车*/
        Route::get('cart', 'CartController@index')->name('cart.index');
        Route::post('cart', 'CartController@store')->name('cart.store');
        Route::delete('cart/{sku}', 'CartController@destory')->name('cart.destory');

        /*订单*/
        Route::get('orders', 'OrdersController@index')->name('orders.index');
        Route::get('orders/{order}/detail', 'OrdersController@detail')->name('orders.detail');
        Route::post('orders', 'OrdersController@store')->name('orders.store');
        Route::get('orders/{order}/show', 'OrdersController@show')->name('orders.show');
        Route::post('orders/{order}/received', 'OrdersController@received')->name('orders.received');
        Route::get('orders/{order}/return', 'OrdersController@return')->name('orders.return');

        /*支付*/
        Route::get('payment/{order}/alipay', 'PaymentController@payByAlipay')->name('payment.alipay');
        Route::get('payment/alipay/return', 'PaymentController@alipayReturn')->name('payment.alipay.return');

        /*退款*/
        Route::post('orders/{order}/apply_refund', 'OrdersController@applyRefund')->name('orders.apply_refund');

        /*收藏*/
        Route::get('products/favorites', 'ProductsController@favorites')->name('products.favorites');
        Route::post('products/{product}/favorite', 'ProductsController@favor')->name('products.favor');
        Route::delete('products/{product}/favorite', 'ProductsController@disfavor')->name('products.disfavor');

        /*评价*/
        Route::get('orders/{order}/review', 'OrdersController@review')->name('orders.review.show');
        Route::post('orders/{order}/review', 'OrdersController@sendReview')->name('orders.review.store');

        /*优惠卷*/
        Route::get('coupon_codes/{code}','CouponCodesController@show')->name('coupon_codes.show');
    });
});

Route::post('payment/alipay/notify', 'PaymentController@alipayNotify')->name('payment.alipay.notify');