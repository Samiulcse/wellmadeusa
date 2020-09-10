<?php

use App\Enumeration\Role;
use Illuminate\Support\Facades\Hash;

Route::get('/', 'HomeController@index')->name('home');

Route::get('/check_orders', 'StaticPageController@checkOrders')->name('check_orders')->middleware('buyer');
// Error Page
Route::get('404',['as'=>'404','uses'=>'ErrorHandlerController@errorCode404']);
Route::get('405',['as'=>'405','uses'=>'ErrorHandlerController@errorCode405']);

Route::get('/about-us', 'StaticPageController@aboutUs')->name('about_us');
Route::get('/look-book', 'StaticPageController@look_book')->name('look_book');
Route::get('/show-schedule', 'StaticPageController@show_schedule')->name('show_schedule');
Route::get('/appointment', 'StaticPageController@appointment')->name('appointment');
Route::post('/appointment', 'StaticPageController@aubmitappontment')->name('submit_appointment_schedule')->middleware('buyer');
Route::get('/contact-us', 'StaticPageController@contactUs')->name('contact_us');
Route::post('/contact_us', 'StaticPageController@contactUsPost')->name('contact_us_post');
Route::get('/terms-conditions', 'StaticPageController@termsConditions')->name('terms_conditions');
Route::get('/privacy-policy', 'StaticPageController@privacyPolicy')->name('privacy_policy');
Route::get('/cookies-policy', 'StaticPageController@cookiesPolicy')->name('cookies_policy');
Route::get('/return-policy', 'StaticPageController@returnPolicy')->name('return_policy');
Route::get('/return-info', 'StaticPageController@returnInfo')->name('return_info');
Route::get('/customer-care', 'StaticPageController@customer_care')->name('customer_care');
Route::get('/size-guide', 'StaticPageController@sizeGuide')->name('size_guide');
Route::get('/payment-shipping', 'StaticPageController@shipping')->name('shipping');
Route::get('/billing-shipping', 'StaticPageController@billingShipping')->name('billing_shipping');
Route::get('/large-quantities', 'StaticPageController@largeQuantities')->name('large_quantities');
Route::get('/refunds', 'StaticPageController@refunds')->name('refunds');
Route::get('/faq', 'StaticPageController@faq')->name('faq');
Route::post('/lookbook/slider', 'StaticPageController@select_lookbook_slider')->name('select_lookbook_slider');

Route::get('/item_details_page_static', 'HomeController@itemDetailsStatic')->name('item_details_page_static');
Route::get('checkout_static', 'Buyer\CheckoutController@checkoutStatic')->name('checkout_static'); 
Route::post('send/customer/email', 'Buyer\AuthController@send_customer_email')->name('send_customer_email_to_admin');
// Sitemap
Route::get('/sitemap', 'SitemapController@index');
Route::get('/sitemap/items', 'SitemapController@items')->name('sitemap_items');
Route::get('/sitemap/vendors', 'SitemapController@vendors')->name('sitemap_vendors');
Route::get('/sitemap/categories', 'SitemapController@categories')->name('vendor_or_parent_category');
Route::get('/sitemap/static', 'SitemapController@staticPages')->name('sitemap_static');
Route::post('mailchip/add', 'HomeController@add_to_mailchimp')->name('add_email_to_mailchimp');

// New Arrival
Route::get('/new-in', 'NewArrivalController@showItems')->name('new_arrival_page');
Route::get('/new_in/{slug}', 'NewArrivalController@NewInSlug');
Route::get('/new_in_ajax', 'NewArrivalController@get_new_arrival_items_load_ajax')->name('get_new_arrival_items_load_ajax');
Route::get('/new_in_filter_ajax', 'NewArrivalController@get_new_arrival_items_filter_load_ajax')->name('get_new_arrival_items_filter_load_ajax');
Route::post('/new_arrival/items', 'NewArrivalController@getNewArrivalItems')->name('get_new_arrival_items');

// Best Seller
Route::get('/best-seller', 'HomeController@bestSellerPage')->name('best_seller_page');

//Best Selling
Route::get('/best-selling', 'CategoryController@bestSelling')->name('best_selling_page');



// Product Item Details
Route::post('/item_rating/{item}', 'HomeController@storeRating')->name('item_rating');
Route::get('/details/{item}', 'HomeController@itemDetails')->name('item_details_page');
Route::post('/quick_view_item', 'HomeController@quickViewItemDetails')->name('quick_view_item');

//Authorize Only
Route::post('/authorize_only', 'Buyer\CheckoutController@authorizeOnly')->name('authorize_only');

//Capture Authorized Amount
Route::post('/authorize_capture_only', 'Buyer\CheckoutController@captureAuthorizedAmount')->name('authorize_capture_only');

// Authorize and Capture
Route::post('/authorize_capture', 'Buyer\CheckoutController@authorizeAndCapture')->name('authorize_capture');

// Post Method Item

// Route::post('/items/get/category', 'HomeController@getItemsCategory')->name('get_items_category');
Route::get('/items/get/category_load', 'HomeController@get_items_category_load_ajax')->name('get_items_category_load_ajax');
Route::get('/items/get/sub_category_load', 'HomeController@get_items_sub_category_load_ajax')->name('get_items_sub_category_load_ajax');

Route::get('/search', 'HomeController@searchPage')->name('search');
Route::post('/search_ajax', 'HomeController@searchAjax')->name('get_search_items_load_ajax');



// Cart
Route::get('cart', 'Buyer\CartController@showCart')->name('show_cart')->middleware('buyer');
Route::post('cart/add', 'Buyer\CartController@addToCart')->name('add_to_cart')->middleware('buyer');
//promo or coupon code
Route::post('checkout/apply_coupon', 'Buyer\CheckoutController@applyCoupon')->name('buyer_apply_coupon')->middleware('buyer');

Route::get('/cart/add/success', 'Buyer\CartController@addToCartSuccess')->name('add_to_cart_success')->middleware('buyer');
Route::post('cart/update', 'Buyer\CartController@updateCart')->name('update_cart')->middleware('buyer');
Route::get('cart/update/success', 'Buyer\CartController@updateCartSuccess')->name('update_cart_success')->middleware('buyer');
Route::post('cart/delete', 'Buyer\CartController@deleteCart')->name('delete_cart')->middleware('buyer');
Route::post('cart/delete/all', 'Buyer\CartController@deleteCartAll')->name('delete_cart_all')->middleware('buyer');

// Auth
Route::get('/register', 'Buyer\AuthController@register')->name('buyer_register');
Route::post('/register/post', 'Buyer\AuthController@registerPost')->name('buyer_register_post');
Route::get('/register/complete', 'Buyer\AuthController@registerComplete')->name('buyer_register_complete');
Route::get('login', 'Buyer\AuthController@login')->name('buyer_login');
Route::post('login/post', 'Buyer\AuthController@loginPost')->name('buyer_login_post');
Route::post('logout', 'Buyer\AuthController@logout')->name('logout_buyer');
Route::get('logout', 'Buyer\AuthController@logout')->name('logout_buyer_get');

Route::get('reset', 'Buyer\AuthController@resetPassword')->name('password_reset_buyer');
Route::post('reset/post', 'Buyer\AuthController@resetPasswordPost')->name('password_reset__buyer_post');
Route::get('reset/new', 'Buyer\AuthController@newPassword')->name('new_password_buyer');
Route::post('reset/new/post', 'Buyer\AuthController@newPasswordPost')->name('new_password_post_buyer');


Route::post('checkout/address/select', 'Buyer\CheckoutController@addressSelect')->name('checkout_address_select')->middleware('buyer');
Route::get('checkout', 'Buyer\CheckoutController@singlePageCheckout')->name('show_checkout')->middleware('buyer');
Route::post('checkout/create', 'Buyer\CheckoutController@create')->name('create_checkout')->middleware('buyer');
Route::post('checkout/single/post', 'Buyer\CheckoutController@singlePageCheckoutPost')->name('single_checkout_post')->middleware('buyer');
Route::get('checkout/complete', 'Buyer\CheckoutController@complete')->name('checkout_complete')->middleware('buyer');


// Order
Route::get('order/{order}', 'Buyer\OtherController@showOrderDetails')->name('show_order_details')->middleware('buyer');
Route::post('order/reject/status', 'Buyer\OtherController@orderRejectStatusChange')->name('order_reject_status_change')->middleware('buyer');
Route::get('order/images/{order}', 'Buyer\OtherController@orderImages')->name('download_order_images')->middleware('buyer');
Route::POST('send_message_buyer', 'Admin\OrderController@send_message_buyer')->name('send_message_buyer')->middleware('employee');

// Buyer Profile Start
Route::get('/profile/dashboard', 'Buyer\ProfileController@buyerDashboard')->name('buyer_show_dashboard')->middleware('buyer');

Route::get('/profile/overview', 'Buyer\ProfileController@overview')->name('buyer_show_overview')->middleware('buyer');
Route::get('profile/message', 'Buyer\ProfileController@message')->name('buyer_show_message')->middleware('buyer');
Route::POST('profile/message/unreadcount', 'Buyer\ProfileController@message_unread_count')->name('buyer_unread_message_count')->middleware('buyer');
Route::post('profile/edit/shipping_info', 'Buyer\ProfileController@updateShippingInfo')->name('buyer_update_shipping_info')->middleware('buyer');
Route::POST('send_message_admin', 'Buyer\ProfileController@send_message_admin')->name('send_message_admin')->middleware('buyer');

Route::get('reset/buyer/new', 'Buyer\AuthController@newBuyerPassword')->name('new_password_buyer_panel');
Route::post('reset/buyer/new/post', 'Buyer\AuthController@newBuyerPasswordPost')->name('new_password_post_buyer_panel');

Route::get('profile/avatar', 'Buyer\ProfileController@getChangeAvatar')->name('buyer_get_change_avatar')->middleware('buyer');
Route::get('profile/billing', 'Buyer\ProfileController@buyerBilling')->name('buyer_billing')->middleware('buyer');
Route::post('profile/billing_address', 'Buyer\ProfileController@editBillingAddress')->name('buyer_update_billing_info')->middleware('buyer');

Route::get('profile/reward-points', 'Buyer\ProfileController@rewardPoints')->name('buyer_show_rewards')->middleware('buyer');

// Wishlist
Route::get('/mysave', 'Buyer\WishListController@mysave')->name('menu_wishlist');
Route::get('wishlist', 'Buyer\WishListController@index')->name('view_wishlist')->middleware('buyer');
Route::post('wishlist/add', 'Buyer\WishListController@addToWishList')->name('add_to_wishlist')->middleware('buyer');
Route::post('wishlist/remove', 'Buyer\WishListController@removeWishListItem')->name('remove_from_wishlist')->middleware('buyer');
Route::post('wishlist/item/details', 'Buyer\WishListController@itemDetails')->name('wishlist_item_details')->middleware('buyer');
Route::post('wishlist/addToCart', 'Buyer\WishListController@addToCart')->name('wishlist_add_to_cart')->middleware('buyer');

// Route::get('profile/overview', 'Buyer\ProfileController@overview')->name('buyer_show_overview')->middleware('buyer');
Route::get('profile/information', 'Buyer\ProfileController@myInformation')->name('buyer_my_information')->middleware('buyer');
Route::get('profile/edit/shipping_info', 'Buyer\ProfileController@editShippingInfo')->name('buyer_edit_shipping_info')->middleware('buyer');

Route::get('profile/edit/shipping_info', 'Buyer\ProfileController@editShippingInfo')->name('buyer_edit_shipping_info')->middleware('buyer');
Route::get('profile/orders', 'Buyer\ProfileController@orders')->name('buyer_show_orders')->middleware('buyer');

// Buyer Profile End

// Route::get('profile/overview', 'Buyer\ProfileController@overview')->name('buyer_show_overview')->middleware('buyer');
Route::get('profile', 'Buyer\ProfileController@index')->name('buyer_show_profile') ;
Route::post('profile/update', 'Buyer\ProfileController@updateProfile')->name('buyer_update_profile')->middleware('buyer');

Route::get('profile/address', 'Buyer\ProfileController@address')->name('buyer_show_address')->middleware('buyer');
Route::post('profile/address', 'Buyer\ProfileController@addressPost')->name('buyer_update_address')->middleware('buyer');
Route::post('profile/add/shipping_address', 'Buyer\ProfileController@addShippingAddress')->name('buyer_add_shipping_address')->middleware('buyer');
Route::post('profile/delete/buillilng_address', 'Buyer\ProfileController@deletebillingAddress')->name('buyer_delete_billing_address')->middleware('buyer');
Route::post('profile/change/default_billing_address', 'Buyer\ProfileController@defaultbillingAddress')->name('buyer_default_billing_address')->middleware('buyer');
Route::post('profile/add/billling_address', 'Buyer\ProfileController@addbillingaddress')->name('buyer_add_billing_address')->middleware('buyer');
Route::post('profile/edit/shipping_address', 'Buyer\ProfileController@editShippingAddress')->name('buyer_edit_shipping_address')->middleware('buyer');
Route::post('profile/change/default_shipping_address', 'Buyer\ProfileController@defaultShippingAddress')->name('buyer_default_shipping_address')->middleware('buyer');
Route::post('profile/delete/shipping_address', 'Buyer\ProfileController@deleteShippingAddress')->name('buyer_delete_shipping_address')->middleware('buyer');
//Route::get('profile/feedback', 'Buyer\ProfileController@feedback')->name('buyer_show_feedback')->middleware('buyer');
//Route::post('profile/feedback/post', 'Buyer\ProfileController@feedbackPost')->name('buyer_feedback_post')->middleware('buyer');
Route::get('orders/print/pdf', 'Buyer\OtherController@printPdf')->name('order_invoice_print_pdf')->middleware('buyer');
Route::get('orders/print/pdf/without_image', 'Buyer\OtherController@printPdfWithOutImage')->name('order_invoice_print_pdf_without_image')->middleware('buyer');

Route::get('profile/avatar', 'Buyer\ProfileController@getChangeAvatar')->name('buyer_get_change_avatar')->middleware('buyer');
Route::post('profile/avatar', 'Buyer\ProfileController@changeAvatar')->name('buyer_change_avatar')->middleware('buyer');

// Notification
Route::get('notification/all', 'Buyer\OtherController@allNotification')->name('view_all_notification')->middleware('buyer');
Route::get('notification/view', 'Buyer\OtherController@viewNotification')->name('view_notification')->middleware('buyer');

Route::prefix('admin')->group(function () {
    // Auth
    Route::get('login', 'Admin\AuthController@login')->name('login_admin');
    Route::post('login/post', 'Admin\AuthController@loginPost')->name('login_admin_post');
    Route::post('logout', 'Admin\AuthController@logout')->name('logout_admin');

    // Dashboard
    Route::get('dashboard', 'Admin\DashboardController@index')->name('admin_dashboard')->middleware('employee');

    // Task
    Route::get('tasks', 'Admin\TasksController@index')->name('tasks')->middleware('employee');
    Route::post('tasks/create', 'Admin\TasksController@create')->name('create_tasks')->middleware('employee');
    Route::post('tasks/delete', 'Admin\TasksController@destroy')->name('delete_tasks')->middleware('employee');


    // Category
    Route::get('category', 'Admin\CategoryController@index')->name('admin_category')->middleware('employee');
    Route::get('category/landing/page', 'Admin\BannerController@category_landing_page')->name('category_landing_page')->middleware('employee');
    Route::post('category/landing/page_post', 'Admin\CategoryController@category_landing_page_post')->name('category_landing_page_post')->middleware('employee');
    Route::post('category/add', 'Admin\CategoryController@addCategory')->name('admin_category_add')->middleware('employee');
    Route::post('category/delete', 'Admin\CategoryController@deleteCategory')->name('admin_category_delete')->middleware('employee');
    Route::post('category/update', 'Admin\CategoryController@updateCategory')->name('admin_category_update')->middleware('employee');
    Route::post('category/update/parent', 'Admin\CategoryController@updateCategoryParent')->name('admin_category_parent_update')->middleware('employee');
    Route::post('category/update/sort', 'Admin\CategoryController@sortCategory')->name('admin_sort_category')->middleware('employee');
    Route::post('category/image/delete', 'Admin\CategoryController@Category_Image_delete')->name('Category_Image_delete')->middleware('employee');
    Route::post('category/detail', 'Admin\CategoryController@categoryDetail')->name('admin_category_detail')->middleware('employee');

    //Admin Message
    Route::get('all/message', 'Admin\AccountSettingController@allMessage')->name('all_message')->middleware('employee');
    Route::POST('all/message/status', 'Admin\AccountSettingController@allMessageStatus')->name('all_message_status')->middleware('employee');

    // Master Color
    Route::get('master_color', 'Admin\MasterColorController@index')->name('admin_master_color')->middleware('employee');
    Route::post('master_color/add/post', 'Admin\MasterColorController@addPost')->name('admin_master_color_add')->middleware('employee');
    Route::post('master_color/delete', 'Admin\MasterColorController@delete')->name('admin_master_color_delete')->middleware('employee');
    Route::post('master_color/update', 'Admin\MasterColorController@update')->name('admin_master_color_update')->middleware('employee');

    // Color
    Route::get('color', 'Admin\ColorController@index')->name('admin_color')->middleware('employee');
    Route::post('color/add/post', 'Admin\ColorController@addPost')->name('admin_color_add_post')->middleware('employee');
    Route::post('color/edit/post', 'Admin\ColorController@editPost')->name('admin_color_edit_post')->middleware('employee');
    Route::post('color/delete', 'Admin\ColorController@delete')->name('admin_color_delete')->middleware('employee');

    // Pack
    Route::get('pack', 'Admin\PackController@index')->name('admin_pack')->middleware('employee');
    Route::post('pack/add/post', 'Admin\PackController@addPost')->name('admin_pack_add_post')->middleware('employee');
    Route::post('pack/edit/post', 'Admin\PackController@editPost')->name('admin_pack_edit_post')->middleware('employee');
    Route::post('pack/delete', 'Admin\PackController@delete')->name('admin_pack_delete')->middleware('employee');
    Route::post('pack/change_status', 'Admin\PackController@changeStatus')->name('admin_pack_change_status')->middleware('employee');
    Route::post('pack/change_default', 'Admin\PackController@changeDefault')->name('admin_pack_change_default')->middleware('employee');

    // Master Fabric
    Route::get('master_fabric', 'Admin\MasterFabricController@index')->name('admin_master_fabric')->middleware('employee');
    Route::post('master_fabric/add/post', 'Admin\MasterFabricController@addPost')->name('admin_master_fabric_add')->middleware('employee');
    Route::post('master_fabric/delete', 'Admin\MasterFabricController@delete')->name('admin_master_fabric_delete')->middleware('employee');
    Route::post('master_fabric/update', 'Admin\MasterFabricController@update')->name('admin_master_fabric_update')->middleware('employee');

    // Items settings other
    Route::get('product_settings/others', 'Admin\ItemSettingsOthersController@index')->name('admin_item_settings_others')->middleware('employee');
    Route::get('product_settings/item_fit_size', 'Admin\ItemSettingsOthersController@item_fit_size')->name('admin_item_fit_size')->middleware('employee');
    Route::post('product_settings/item_fit_size_add', 'Admin\ItemSettingsOthersController@item_fit_size_add')->name('admin_item_fit_size_add')->middleware('employee');

    // Made In Country
    Route::post('item_settings/made_in_country/add/post', 'Admin\ItemSettingsOthersController@madeInCountryAdd')->name('admin_made_in_country_add')->middleware('employee');
    Route::post('item_settings/made_in_country/update/post', 'Admin\ItemSettingsOthersController@madeInCountryUpdate')->name('admin_made_in_country_update')->middleware('employee');
    Route::post('item_settings/made_in_country/delete/post', 'Admin\ItemSettingsOthersController@madeInCountryDelete')->name('admin_made_in_country_delete')->middleware('employee');
    Route::post('item_settings/made_in_country/change_status/post', 'Admin\ItemSettingsOthersController@madeInCountryChangeStatus')->name('admin_made_in_country_change_status')->middleware('employee');
    Route::post('item_settings/made_in_country/change_default/post', 'Admin\ItemSettingsOthersController@madeInCountryChangeDefault')->name('admin_made_in_country_change_default')->middleware('employee');
    Route::post('items/list-clone', 'Admin\ItemController@cloneMultiItems')->name('admin_clone_multi_items')->middleware('employee');

    //material and care
    Route::post('item_settings/material/add/post', 'Admin\ItemSettingsOthersController@materialAdd')->name('admin_material_add')->middleware('employee');
    Route::post('item_settings/material/update/post', 'Admin\ItemSettingsOthersController@materialUpdate')->name('admin_material_update')->middleware('employee');
    Route::post('item_settings/material/delete/post', 'Admin\ItemSettingsOthersController@materialDelete')->name('admin_material_delete')->middleware('employee');
    Route::post('item_settings/material/change_status/post', 'Admin\ItemSettingsOthersController@materialChangeStatus')->name('admin_material_change_status')->middleware('employee');

    //bullet two
    Route::post('item_settings/bullet_two/add/post', 'Admin\ItemSettingsOthersController@bulletTwoAdd')->name('admin_bullet_two_add')->middleware('employee');
    Route::post('item_settings/bullet_two/update/post', 'Admin\ItemSettingsOthersController@bulletTwoUpdate')->name('admin_bullet_two_update')->middleware('employee');
    Route::post('item_settings/bullet_two/delete/post', 'Admin\ItemSettingsOthersController@bulletTwoDelete')->name('admin_bullet_two_delete')->middleware('employee');
    Route::post('item_settings/bullet_two/change_status/post', 'Admin\ItemSettingsOthersController@bulletTwoChangeStatus')->name('admin_bullet_two_change_status')->middleware('employee');

    //bullet three
    Route::post('item_settings/bullet_three/add/post', 'Admin\ItemSettingsOthersController@bulletThreeAdd')->name('admin_bullet_three_add')->middleware('employee');
    Route::post('item_settings/bullet_three/update/post', 'Admin\ItemSettingsOthersController@bulletThreeUpdate')->name('admin_bullet_three_update')->middleware('employee');
    Route::post('item_settings/bullet_three/delete/post', 'Admin\ItemSettingsOthersController@bulletThreeDelete')->name('admin_bullet_three_delete')->middleware('employee');
    Route::post('item_settings/bullet_three/change_status/post', 'Admin\ItemSettingsOthersController@bulletThreeChangeStatus')->name('admin_bullet_three_change_status')->middleware('employee');

    //bullet four
    Route::post('item_settings/bullet_four/add/post', 'Admin\ItemSettingsOthersController@bulletFourAdd')->name('admin_bullet_four_add')->middleware('employee');
    Route::post('item_settings/bullet_four/update/post', 'Admin\ItemSettingsOthersController@bulletFourUpdate')->name('admin_bullet_four_update')->middleware('employee');
    Route::post('item_settings/bullet_four/delete/post', 'Admin\ItemSettingsOthersController@bulletFourDelete')->name('admin_bullet_four_delete')->middleware('employee');
    Route::post('item_settings/bullet_four/change_status/post', 'Admin\ItemSettingsOthersController@bulletFourChangeStatus')->name('admin_bullet_four_change_status')->middleware('employee');

    // Fabric
    Route::post('item_settings/fabric/add/post', 'Admin\ItemSettingsOthersController@fabricAdd')->name('admin_fabric_add')->middleware('employee');
    Route::post('item_settings/fabric/update/post', 'Admin\ItemSettingsOthersController@fabricUpdate')->name('admin_fabric_update')->middleware('employee');
    Route::post('item_settings/fabric/delete/post', 'Admin\ItemSettingsOthersController@fabricDelete')->name('admin_fabric_delete')->middleware('employee');
    Route::post('item_settings/fabric/change_status/post', 'Admin\ItemSettingsOthersController@fabricChangeStatus')->name('admin_fabric_change_status')->middleware('employee');
    Route::post('item_settings/fabric/change_default/post', 'Admin\ItemSettingsOthersController@fabricChangeDefault')->name('admin_fabric_change_default')->middleware('employee');

    // Create a new item
    Route::get('create_new_item', 'Admin\ItemController@createNewItemIndex')->name('admin_create_new_item')->middleware('employee');
    Route::post('create_new_item/post', 'Admin\ItemController@createNewItemPost')->name('admin_create_new_item_post')->middleware('employee');
    Route::post('create_new_item/upload/image', 'Admin\ItemController@uploadImage')->name('admin_item_upload_image')->middleware('employee');
    Route::post('create_new_item/add/color', 'Admin\ItemController@addColor')->name('admin_item_add_color')->middleware('employee');

    Route::post('edit_new_item/remove_video', 'Admin\ItemController@removeVideo')->name('admin_item_remove_video')->middleware('employee');

    // Edit Item
    Route::get('item/edit/{item}', 'Admin\ItemController@editItem')->name('admin_edit_item')->middleware('employee');
    Route::post('item/edit/{item}', 'Admin\ItemController@editItemPost')->name('admin_edit_item_post')->middleware('employee');

    // Clone Item
    Route::get('item/clone/{item}', 'Admin\ItemController@cloneItem')->name('admin_clone_item')->middleware('employee');
    Route::post('item/clone/{old_item}', 'Admin\ItemController@cloneItemPost')->name('admin_clone_item_post')->middleware('employee');

    // Item List
    Route::get('items/all', 'Admin\ItemController@itemListAll')->name('admin_item_list_all')->middleware('employee');
    Route::post('item_list/change_to_inactive', 'Admin\ItemController@itemsChangeToInactive')->name('admin_item_list_change_to_inactive')->middleware('employee');
    Route::post('item_list/change_to_active', 'Admin\ItemController@itemsChangeToActive')->name('admin_item_list_change_to_active')->middleware('employee');
    Route::post('item_list/delete', 'Admin\ItemController@itemsDelete')->name('admin_item_list_delete')->middleware('employee');
    Route::get('item/category/{category}', 'Admin\ItemController@itemListByCategory')->name('admin_item_list_by_category')->middleware('employee');

    Route::post('items/category/move', 'Admin\ItemController@categoryMove')->name('admin_category_move')->middleware('employee');
    // Item Import
    Route::get('items/import', 'Admin\ItemController@dataImportView')->name('admin_data_import')->middleware('employee');
    Route::post('items/import/read_file', 'Admin\ItemController@dataImportReadFile')->name('admin_data_import_read_file');
    Route::post('items/import/upload', 'Admin\ItemController@dataImportUpload')->name('admin_data_import_upload')->middleware('employee');
    Route::post('items/import/images', 'Admin\ItemController@dataImportImage')->name('admin_data_import_image')->middleware('employee');

    // item statistics
    Route::get('/item/statistics', 'Admin\ItemStatisticsController@index')->name('item_statistics')->middleware('employee');
    Route::POST('/item/statistics/filter', 'Admin\ItemStatisticsController@filter')->name('item_statistics_filter')->middleware('employee');
    Route::POST('/item/statistics/get_quantity_of_color_product', 'Admin\ItemStatisticsController@get_quantity_of_color_product')->name('qty_c_products')->middleware('employee');
    Route::POST('/item/statistics/stylenoSearch', 'Admin\ItemStatisticsController@stylenoSearch')->name('stylenoSearch')->middleware('employee');

    // Banner
    Route::get('banner', 'Admin\BannerController@index')->name('admin_banner')->middleware('employee');
    Route::post('banner/add/post', 'Admin\BannerController@addPost')->name('admin_banner_add_post')->middleware('employee');
    Route::post('banner/delete', 'Admin\BannerController@delete')->name('admin_banner_delete')->middleware('employee');
    Route::post('banner/active', 'Admin\BannerController@active')->name('admin_banner_active')->middleware('employee');
    Route::get('banner/recommend_banner', 'Admin\BannerController@frontRecommendBanner')->name('admin_front_recommend_banner')->middleware('employee');
    Route::post('banner/uploadBanner', 'Admin\BannerController@uploadBanner')->name('admin_upload_banners')->middleware('employee');
    Route::post('banner/remove', 'Admin\BannerController@removeBanner')->name('admin_remove_banners')->middleware('employee');
    Route::post('banner/top_slider/delete', 'Admin\BannerController@top_front_slider_item_delete')->name('top_front_slider_item_delete')->middleware('employee');
    Route::get('banner/front_page/add', 'Admin\BannerController@front_page_recommend_banner_add')->name('front_page_recommend_banner_add')->middleware('employee');

    Route::get('banner/admin_new_arrival', 'Admin\BannerController@NewArrivalPageBanner')->name('admin_new_arrival')->middleware('employee');
    Route::post('homepage/settings/update', 'Admin\BannerController@update_home_page_settings')->name('admin_update_home_Settings')->middleware('employee');


    // Logo
    Route::post('logo/add/post', 'Admin\BannerController@logoPost')->name('admin_logo_add_post')->middleware('employee');
    // Add New Season
    

    // Banner Items
    Route::get('banner/items', 'Admin\BannerController@bannerItems')->name('admin_banner_items')->middleware('employee');
    Route::post('banner/item/add', 'Admin\BannerController@bannerItemAdd')->name('admin_banner_item_add')->middleware('employee');
    Route::post('banner/item/remove', 'Admin\BannerController@bannerItemRemove')->name('admin_banner_item_remove')->middleware('employee');
    Route::post('banner/item/sort', 'Admin\BannerController@bannerItemsSort')->name('admin_banner_item_sort')->middleware('employee');

    // Banner Main Slider
    Route::get('banner/main_slider', 'Admin\BannerController@mainSliderItems')->name('admin_main_slider_items')->middleware('employee');
    Route::post('banner/main_slider/add', 'Admin\BannerController@mainSliderItemAdd')->name('admin_main_slider_item_add')->middleware('employee');
    Route::post('banner/main_slider/sort', 'Admin\BannerController@mainSliderItemsSort')->name('admin_main_slider_items_sort')->middleware('employee');
    Route::post('banner/main_slider/delete', 'Admin\BannerController@mainSliderItemDelete')->name('admin_main_slider_item_delete')->middleware('employee');

    // Banner Front Page (Section 1)
    Route::get('banner/front_page', 'Admin\BannerController@frontPageBannerItems')->name('admin_front_page_banner_items')->middleware('employee');
    Route::post('banner/front_page/add', 'Admin\BannerController@frontPageBannerItemAdd')->name('admin_front_page_banner_item_add')->middleware('employee');
    Route::get('banner/second_banner/add', 'Admin\BannerController@frontPageBannerItemAddSecond')->name('admin_front_page_sec_banner_add')->middleware('employee');
    Route::post('banner/edit', 'Admin\BannerController@editPost')->name('admin_banner_edit_post')->middleware('employee');

    // Banner Front Page (Section 2)
    Route::get('banner/front_page_two', 'Admin\BannerController@frontPageBannerTwo')->name('admin_front_page_banner_two')->middleware('employee');
    Route::post('banner/front_page_two/add', 'Admin\BannerController@frontPageBannerTwoAdd')->name('admin_front_page_banner_two_add')->middleware('employee');
    Route::post('banner/edit_two', 'Admin\BannerController@frontPageBannerTwoEdit')->name('admin_banner_two_edit_post')->middleware('employee');
    Route::post('banner/content/save', 'Admin\BannerController@category_landing_page_custom_content')->name('category_landing_page_custom_content')->middleware('employee');

     // Banner Front Page (Section 3)
    Route::get('banner/front_page_three', 'Admin\BannerController@frontPageBannerThree')->name('admin_front_page_banner_three')->middleware('employee');
    Route::post('banner/front_page_three/add', 'Admin\BannerController@frontPageBannerThreeAdd')->name('admin_front_page_banner_three_add')->middleware('employee');
    Route::post('banner/edit_three', 'Admin\BannerController@frontPageBannerThreeEdit')->name('admin_banner_three_edit_post')->middleware('employee');

    // Banner Front Page (Section 4)
    Route::get('banner/front_page_four', 'Admin\BannerController@frontPageBannerFour')->name('admin_front_page_banner_four')->middleware('employee');
    Route::post('banner/front_page_four/add', 'Admin\BannerController@frontPageBannerFourAdd')->name('admin_front_page_banner_four_add')->middleware('employee');
    Route::post('banner/edit_four', 'Admin\BannerController@frontPageBannerFourEdit')->name('admin_banner_four_edit_post')->middleware('employee');

    // Banner Top
    Route::get('banner/top', 'Admin\BannerController@topBanner')->name('admin_top_banners')->middleware('employee');
    Route::post('banner/top/add', 'Admin\BannerController@topBannerAdd')->name('admin_top_banner_add')->middleware('employee');
    Route::post('banner/top/edit', 'Admin\BannerController@topBannerEditPost')->name('admin_top_banner_edit_post')->middleware('employee');
    Route::post('banner/top/delete', 'Admin\BannerController@topBannerDelete')->name('admin_top_banner_delete')->middleware('employee');
    Route::post('banner/logo/remove', 'Admin\BannerController@LogoItemRemove')->name('admin_logo_item_remove')->middleware('employee');
    Route::get('banner/search', 'Admin\BannerController@BannerAllSearchItem')->name('BannerAllSearchItem')->middleware('employee');

    // Notification Bannar
    Route::get('notification/banner', 'Admin\NotificationBannerController@index')->name('admin_notification_banner')->middleware('employee');
    Route::post('notification/banner/add/post', 'Admin\NotificationBannerController@addPost')->name('admin_notification_banner_add_post')->middleware('employee');
    Route::post('notification/banner/delete', 'Admin\NotificationBannerController@delete')->name('admin_notification_banner_delete')->middleware('employee');
    Route::post('banner/active', 'Admin\BannerController@active')->name('admin_banner_active')->middleware('employee');

    // Notification Bannar
    Route::get('footer/banner', 'Admin\NotificationBannerController@footer_index')->name('admin_footer_banner')->middleware('employee');
    Route::post('footer/banner/add/post', 'Admin\NotificationBannerController@footer_addPost')->name('admin_footer_banner_add_post')->middleware('employee');
    Route::post('footer/banner/delete', 'Admin\NotificationBannerController@delete')->name('admin_footer_banner_delete')->middleware('employee');



    // Administration -> Vendor Information
    Route::get('administration/admin_information', 'Admin\VendorInformationController@index')->name('admin_admin_information')->middleware('employee');
    Route::post('administration/company_information/post', 'Admin\VendorInformationController@companyInformationPost')->name('admin_company_information_post')->middleware('employee');
    Route::post('administration/size_chart/post', 'Admin\VendorInformationController@sizeChartPost')->name('admin_size_chart_post')->middleware('employee');
    Route::post('administration/order_notice/post', 'Admin\VendorInformationController@orderNoticePost')->name('admin_order_notice_post')->middleware('employee');
    Route::post('administration/shipping/post', 'Admin\VendorInformationController@admin_shipping_post')->name('admin_shipping_post')->middleware('employee');
    Route::post('administration/style_pick/post', 'Admin\VendorInformationController@stylePickPost')->name('admin_style_pick_post')->middleware('employee');
    Route::post('administration/save/settings', 'Admin\VendorInformationController@saveSetting')->name('admin_save_setting_post')->middleware('employee');
    Route::post('administration/save/return', 'Admin\VendorInformationController@savereturn_info')->name('administration_return_info')->middleware('employee');

    // Account Setting
    Route::get('administration/account_setting', 'Admin\AccountSettingController@index')->name('admin_account_setting')->middleware('employee');
    Route::post('administration/admin_id/post', 'Admin\AccountSettingController@adminIdPost')->name('admin_admin_id_post')->middleware('employee');
    Route::post('administration/manage_account/add/post', 'Admin\AccountSettingController@addAccountPost')->name('admin_add_account_post')->middleware('employee');
    Route::post('administration/manage_account/delete/post', 'Admin\AccountSettingController@deleteAccountPost')->name('admin_delete_account_post')->middleware('employee');
    Route::post('administration/manage_account/update/post', 'Admin\AccountSettingController@updateAccountPost')->name('admin_update_account_post')->middleware('employee');
    Route::post('administration/manage_account/status_update/post', 'Admin\AccountSettingController@statusUpdateAccountPost')->name('admin_status_update_account_post')->middleware('employee');
    Route::post('administration/store_setting/save/post', 'Admin\AccountSettingController@saveStoreSetting')->name('admin_save_store_setting_post')->middleware('employee');

    Route::get('administration/admin_message', 'Admin\AccountSettingController@admin_message')->name('admin_message')->middleware('employee');
    Route::POST('administration/message/message_status', 'Admin\AccountSettingController@admin_message_status')->name('admin_message_status')->middleware('employee');


    // Courier
    Route::get('courier', 'Admin\CourierController@index')->name('admin_courier')->middleware('employee');
    Route::post('courier/add/post', 'Admin\CourierController@addPost')->name('admin_courier_add')->middleware('employee');
    Route::post('courier/delete', 'Admin\CourierController@delete')->name('admin_courier_delete')->middleware('employee');
    Route::post('courier/update', 'Admin\CourierController@update')->name('admin_courier_update')->middleware('employee');

    // Promotions
    Route::get('promotions', 'Admin\PromotionController@index')->name('admin_promotions')->middleware('employee');
    Route::post('promotions/add/post', 'Admin\PromotionController@addPost')->name('admin_promotions_add_post')->middleware('employee');
    Route::post('promotions/edit/post', 'Admin\PromotionController@editPost')->name('admin_promotions_edit_post')->middleware('employee');
    Route::post('promotions/delete', 'Admin\PromotionController@delete')->name('admin_promotions_delete')->middleware('employee');

    // Point system
    Route::get('point-system', 'Admin\PointSystemController@index')->name('admin_point_system')->middleware('employee');
    Route::post('point-settings', 'Admin\PointSystemController@saveSetting')->name('admin_save_point_system_setting_post')->middleware('employee');
    Route::post('discount-settings', 'Admin\PointSystemController@saveDiscount')->name('admin_save_discount_setting_post')->middleware('employee');
    Route::post('points/add/post', 'Admin\PointSystemController@addPoint')->name('admin_points_add_post')->middleware('employee');
    Route::post('points/edit/post', 'Admin\PointSystemController@editPoint')->name('admin_points_edit_post')->middleware('employee');
    Route::post('points/delete', 'Admin\PointSystemController@delete')->name('admin_points_delete')->middleware('employee');


    // Ship Method
    Route::get('ship_method', 'Admin\ShipMethodController@index')->name('admin_ship_method')->middleware('employee');
    Route::post('ship_method/add/post', 'Admin\ShipMethodController@addPost')->name('admin_ship_method_add')->middleware('employee');
    Route::post('ship_method/delete', 'Admin\ShipMethodController@delete')->name('admin_ship_method_delete')->middleware('employee');
    Route::post('ship_method/update', 'Admin\ShipMethodController@update')->name('admin_ship_method_update')->middleware('employee');

    // Coupon
    Route::get('coupon', 'Admin\CouponController@index')->name('admin_coupon')->middleware('employee');
    Route::post('coupon/add/post', 'Admin\CouponController@addPost')->name('admin_coupon_add_post')->middleware('employee');
    Route::post('coupon/edit/post', 'Admin\CouponController@editPost')->name('admin_coupon_edit_post')->middleware('employee');
    Route::post('coupon/delete', 'Admin\CouponController@delete')->name('admin_coupon_delete')->middleware('employee');

    // Promo codes
    Route::get('promo_codes', 'Admin\PromoCodesController@index')->name('admin_promo_codes')->middleware('employee');
    Route::post('promo_codes/add/post', 'Admin\PromoCodesController@addPost')->name('admin_promo_codes_add')->middleware('employee');
    Route::post('promo_codes/delete', 'Admin\PromoCodesController@delete')->name('admin_promo_codes_delete')->middleware('employee');
    Route::post('promo_codes/update', 'Admin\PromoCodesController@update')->name('admin_promo_codes_update')->middleware('employee');

    Route::post('add-short-desc', 'Admin\CouponController@add_header_short_desc')->name('add_header_short_desc')->middleware('employee');
    Route::post('update-bonus', 'Admin\CouponController@update_minimum_bonus_amount')->name('update_minimum_bonus_amount')->middleware('employee');


    // Social links
    Route::get('social_links', 'Admin\SocialController@index')->name('admin_social_link')->middleware('employee');
    Route::post('social_links', 'Admin\SocialController@addUpdatePost')->name('admin_social_links_add_post')->middleware('employee');

    // Header footer color
    Route::get('header_footer_color', 'Admin\SettingsController@color')->name('admin_header_footer_color')->middleware('employee');
    Route::post('header_footer_color', 'Admin\SettingsController@postColor')->name('admin_header_footer_color_post')->middleware('employee');

    //Social Feed
    Route::get('social_feeds', 'Admin\SocialController@social_feed_access')->name('admin_social_feed')->middleware('employee');
    Route::post('social_feeds', 'Admin\SocialController@socialFeedaddUpdatePost')->name('admin_social_feed_add_post')->middleware('employee');

    // Orders
    Route::get('orders/all', 'Admin\OrderController@allOrders')->name('admin_all_orders')->middleware('employee');
    Route::get('orders/type', 'Admin\OrderController@admin_orders_according_type')->name('admin_orders_according_type')->middleware('employee');
    Route::get('orders/new', 'Admin\OrderController@newOrders')->name('admin_new_orders')->middleware('employee');
    Route::get('orders/confirmed', 'Admin\OrderController@confirmOrders')->name('admin_confirmed_orders')->middleware('employee');
    Route::get('orders/backed', 'Admin\OrderController@backedOrders')->name('admin_backed_orders')->middleware('employee');
    Route::get('orders/shipped', 'Admin\OrderController@shippedOrders')->name('admin_shipped_orders')->middleware('employee');
    Route::get('orders/cancelled', 'Admin\OrderController@cancelledOrders')->name('admin_cancelled_orders')->middleware('employee');
    Route::get('orders/returned', 'Admin\OrderController@returnedOrders')->name('admin_returned_orders')->middleware('employee');
    Route::get('orders/details/{order}', 'Admin\OrderController@orderDetails')->name('admin_order_details')->middleware('employee');
    Route::post('orders/details/post/{order}', 'Admin\OrderController@orderDetailsPost')->name('admin_order_details_post')->middleware('employee');
    Route::get('orders/incomplete', 'Admin\OrderController@incompleteOrders')->name('admin_incomplete_orders')->middleware('employee');
    Route::get('orders/incomplete/{user}', 'Admin\OrderController@incompleteOrderDetails')->name('admin_incomplete_order_detail')->middleware('employee');
    Route::post('orders/backorder/create', 'Admin\OrderController@createBackorder')->name('admin_create_back_order')->middleware('employee');
    Route::post('orders/out_of_stock', 'Admin\OrderController@outOfStock')->name('admin_out_of_stock')->middleware('employee');
    Route::post('orders/delete_item', 'Admin\OrderController@deleteOrderItem')->name('admin_delete_order_item')->middleware('employee');
    Route::post('order/delete', 'Admin\OrderController@deleteOrder')->name('admin_delete_order')->middleware('employee');
    Route::post('order/item_details', 'Admin\OrderController@itemDetails')->name('admin_get_item_details')->middleware('employee');
    Route::post('order/add/item', 'Admin\OrderController@addItem')->name('admin_order_add_item')->middleware('employee');
    Route::post('order/change/status', 'Admin\OrderController@changeStatus')->name('admin_change_order_status')->middleware('employee');

    //Order by Company New Window
    Route::get('company-order/{user_id}','Admin\OrderController@companyOrder')->name('order_by_company')->middleware('employee');

    Route::get('orders/print/pdf', 'Admin\OrderController@printPdf')->name('admin_print_pdf')->middleware('employee');
    Route::get('orders/print/pdf/without_image', 'Admin\OrderController@printPdfWithOutImage')->name('admin_print_pdf_without_image')->middleware('employee');
    Route::get('orders/print/packlist', 'Admin\OrderController@printPacklist')->name('admin_print_packlist')->middleware('employee');

    Route::post('orders/check_password', 'Admin\OrderController@checkPassword')->name('admin_order_check_password')->middleware('employee');
    Route::post('orders/mask/card_number', 'Admin\OrderController@maskCardNumber')->name('admin_order_mask_card_number')->middleware('employee');

    //shipping order export
    Route::post('export-shipping-information', 'Admin\OrderController@exportShipping')->name('export_shipping_information')->middleware('employee');
    Route::post('export-back-information', 'Admin\OrderController@exportBack')->name('export_back_information')->middleware('employee');
    Route::post('export-new-information', 'Admin\OrderController@exportNew')->name('export_new_information')->middleware('employee');
    Route::post('export-cancel-information', 'Admin\OrderController@exportCancel')->name('export_cancel_information')->middleware('employee');
    Route::post('export-confirm-information', 'Admin\OrderController@exportConfirm')->name('export_confirm_information')->middleware('employee');

    //Incomplete order send mail
    Route::post('orders/incomplete/sendmail', 'Admin\OrderController@incompleteOrderSendMail')->name('admin_incomplete_order_send_mail')->middleware('employee');

    // Admin Orders
    Route::get('orders/create', 'Admin\AdminNewOrderController@itemListAll')->name('admin_new_order_create')->middleware('employee');
    Route::POST('set_session', 'Admin\AdminNewOrderController@set_session')->name('set_session')->middleware('employee');
    Route::post('orders/set_new_customer', 'Admin\AdminNewOrderController@set_new_customer')->name('set_new_customer')->middleware('employee');
    Route::post('admin/name/autocomplete', 'Admin\AdminNewOrderController@nameSearch')->name('admin_name_autocomplete')->middleware('employee');
    Route::post('admin/company/autocomplete', 'Admin\AdminNewOrderController@companySearch')->name('admin_company_autocomplete')->middleware('employee');

    // Admin Cart
    Route::get('cart/item/color', 'Admin\AdminCartController@cartItemColor')->name('cart_item_color')->middleware('employee');
    Route::post('cart/item/add', 'Admin\AdminCartController@addToCart')->name('cart_item_add')->middleware('employee');
    Route::get('admin_cart/', 'Admin\AdminCartController@showCart')->name('show_admin_cart')->middleware('employee');
    Route::post('admin_cart_delete/', 'Admin\AdminCartController@deleteCart')->name('admin_delete_cart')->middleware('employee');
    Route::post('cart/update/admin', 'Admin\AdminCartController@updateCart')->name('update_cart_admin')->middleware('employee');
    Route::get('admin/cart/update/success', 'Admin\AdminCartController@updateCartSuccess')->name('admin_update_cart_success')->middleware('employee');

    // Admin Checkout
    Route::post('admin/add/shipping_address', 'Admin\AdminCheckoutController@addShippingAddress')->name('admin_add_shipping_address')->middleware('employee');
    Route::post('admin/checkout/apply_coupon', 'Admin\AdminCheckoutController@applyCoupon')->name('admin_apply_coupon')->middleware('employee');
    Route::get('admin/checkout', 'Admin\AdminCheckoutController@singlePageCheckout')->name('admin_show_checkout')->middleware('employee');
    Route::post('admin/checkout/create', 'Admin\AdminCheckoutController@create')->name('create_admin_checkout')->middleware('employee');
    Route::post('admin/checkout/post', 'Admin\AdminCheckoutController@singlePageCheckoutPost')->name('admin_checkout_post')->middleware('employee');
    Route::get('admin/checkout/complete', 'Admin\AdminCheckoutController@complete')->name('admin_checkout_complete')->middleware('employee');



    // Buyer Home
    Route::get('buyer_home', 'Admin\OtherController@buyerHome')->name('admin_buyer_home')->middleware('employee');
    Route::post('buyer_home/save', 'Admin\OtherController@buyerHomeSave')->name('admin_buyer_home_save')->middleware('employee');

    // Welcome Notification
    Route::get('welcome_notification', 'Admin\OtherController@welcomeNotification')->name('admin_welcome_notification')->middleware('employee');
    Route::post('welcome_notification/save', 'Admin\OtherController@welcomeNotificationSave')->name('admin_welcome_notification_save')->middleware('employee');

    Route::get('top_notification', 'Admin\OtherController@topNotification')->name('admin_top_notification')->middleware('employee');
    Route::post('top_notification/save', 'Admin\OtherController@topNotificationSave')->name('admin_top_notification_save')->middleware('employee');

    // Customer
    Route::get('customer/all', 'Admin\BuyerController@allBuyer')->name('admin_all_buyer')->middleware('employee');
    // import customer
    Route::post('customer/import', 'Admin\BuyerController@importCustomer')->name('adminImportCustomer')->middleware('employee');

    Route::post('customer/change/status', 'Admin\BuyerController@changeStatus')->name('admin_buyer_change_status')->middleware('employee');
    Route::post('customer/change/verified', 'Admin\BuyerController@changeVerified')->name('admin_buyer_change_verified')->middleware('employee');
    Route::post('customer/change/mailing_list', 'Admin\BuyerController@changeMailingList')->name('admin_buyer_change_mailing_list')->middleware('employee');
    Route::post('customer/change/block', 'Admin\BuyerController@changeBlock')->name('admin_buyer_change_block')->middleware('employee');
    Route::post('customer/change/minOrder', 'Admin\BuyerController@changeMinOrder')->name('admin_buyer_change_min_order')->middleware('employee');
    Route::get('customer/edit/{buyer}', 'Admin\BuyerController@edit')->name('admin_buyer_edit')->middleware('employee');
    Route::post('customer/edit/post/{buyer}', 'Admin\BuyerController@editPost')->name('admin_buyer_edit_post')->middleware('employee');
    Route::get('customer/export', 'Admin\BuyerController@allBuyerExport')->name('admin_all_buyer_export')->middleware('employee');
    Route::post('customer/delete', 'Admin\BuyerController@delete')->name('admin_buyer_delete')->middleware('employee');
    Route::post('customer/send/mail', 'Admin\BuyerController@buyerSendMail')->name('admin_send_mail_buyer')->middleware('employee');

    //customer create from admin panel
    Route::get('customer/create', 'Admin\BuyerController@customerCreate')->name('customer_create')->middleware('employee');
    Route::post('/customer/post', 'Admin\BuyerController@customerPost')->name('customer_register_post')->middleware('employee');
    Route::get('/customer/complete', 'Admin\BuyerController@customerComplete')->name('customer_register_complete')->middleware('employee');

    // Store Credit
    Route::post('/store_credit/add', 'Admin\StoreCreditController@add')->name('admin_add_store_credit')->middleware('employee');
    Route::get('/store_credit', 'Admin\StoreCreditController@view')->name('admin_store_credit_view')->middleware('employee');
    Route::post('/store_credit/update', 'Admin\StoreCreditController@update')->name('admin_store_credit_update')->middleware('employee');

    // Feedback
    /*Route::get('feedback', 'Admin\ReviewController@index')->name('admin_feedback')->middleware('employee');
    Route::post('feedback/save', 'Admin\ReviewController@saveFeedback')->name('admin_save_feedback')->middleware('employee');*/

    // Export to SP
    Route::get('export/sp', 'Admin\OtherController@exportToSPView')->name('admin_export_to_sp_view')->middleware('employee');
    Route::post('export/sp', 'Admin\OtherController@exportToSPPost')->name('admin_export_to_sp_post')->middleware('employee');

    // Sort Items
    Route::get('sort/items', 'Admin\SortController@index')->name('admin_sort_items_view')->middleware('employee');
    Route::post('sort/items/save', 'Admin\SortController@save')->name('admin_sort_items_save')->middleware('employee');

    // Pages
    Route::get('page/{id}', 'Admin\PageController@index')->name('admin_page_view')->middleware('employee');
    Route::post('page/save{id}', 'Admin\PageController@save')->name('admin_page_save')->middleware('employee');
    Route::get('/lookbook/', 'Admin\PageController@show_schedule_page')->name('schedule_page')->middleware('employee');
    Route::post('lookbook/add', 'Admin\PageController@add_new_season')->name('add_new_seson')->middleware('employee');
    Route::post('season/add', 'Admin\PageController@new_lookbook_season')->name('add_new_lookbook_season')->middleware('employee');
    Route::post('season/delete', 'Admin\PageController@admin_season_delete')->name('admin_season_delete')->middleware('employee');
    Route::post('season/default', 'Admin\PageController@set_default_season')->name('set_default_season')->middleware('employee');
    
    Route::get('appointments/', 'Admin\PageController@Appointment')->name('appointments')->middleware('employee');
    Route::post('add/apoint/time/', 'Admin\PageController@add_new_appoint_time')->name('add_new_appoint_time')->middleware('employee');
    Route::post('appointment/delete/', 'Admin\PageController@appointment_delete')->name('appointment_delete')->middleware('employee');
    Route::post('time/delete/', 'Admin\PageController@appointTime_delete')->name('appointTime_delete')->middleware('employee');

    // Meta
    Route::get('meta/page/{page}', 'Admin\MetaController@page')->name('admin_meta_page')->middleware('employee');
    Route::get('meta/category/{category}', 'Admin\MetaController@category')->name('admin_meta_category')->middleware('employee');
    Route::get('meta/vendor/{vendor}', 'Admin\MetaController@vendor')->name('admin_meta_vendor')->middleware('employee');
    Route::post('meta/save', 'Admin\MetaController@save')->name('admin_meta_save')->middleware('employee');

    // Others
    Route::post('modal/items', 'Admin\OtherController@getItems')->name('admin_get_items_for_modal')->middleware('employee');
    //import customer data
    Route::get('/read-excel',function(){
        $fileD = fopen(public_path('customers5.csv'), "r");
        $column = fgetcsv($fileD);
        while (!feof($fileD)) {
            $rowData[] = fgetcsv($fileD);
        }
        foreach ($rowData as $key => $value) {
            $inserted_data = array('company_name' => $value[0], 'first_name' => $value[1], 'email' => $value[2], 'address' => $value[3], 'unit' => $value[4],
                'phone' => $value[5], 'city' => $value[6], 'state' => $value[7], 'zipcode' => $value[8],
                'fax' => $value[9], 'website' => $value[10], 'approved' => $value[11], 'approved_at' => $value[12], 'created_at' => $value[13],
                'orders' => $value[14], 'logins' => $value[15], 'last_login' => $value[16], 'password' => $value[17]);
            $name = explode(' ', $inserted_data['first_name']);
            $approvedAt=null;
            $createdAt = null;
            $lastLogin = null;
            if ($approvedAt) {

                $approvedAt = explode(' ', $inserted_data['approved_at']);
            }
            if ($createdAt) {

                $createdAt = explode(' ', $inserted_data['created_at']);
            }
            if ($lastLogin) {

                $lastLogin = explode(' ', $inserted_data['last_login']);
            }

            $userCheck = App\Model\User::where('email', $inserted_data['email'])->first();

            if ($inserted_data['approved'] == 'Y' && !isset($userCheck)) {
                $meta = App\Model\MetaBuyer::create([
                    'verified' => 1,
                    'active' => ($inserted_data['approved'] == 'Y') ? 1 : 0,
                    'user_id' => 0,
                    'primaryCustomerMarket' => 1,
                    'primary_customer_market' => 1,
                    'seller_permit_number' => '123456',
                    'company_name' => $inserted_data['company_name'],
                    'website' => $inserted_data['website'],
                    'billing_location' => (!isset($inserted_data['address'])) ? 'INT' : 'US',
                    'billing_address' => $inserted_data['address'],
                    'billing_unit' => $inserted_data['unit'],
                    'billing_state' => $inserted_data['state'],
                    'billing_city' => $inserted_data['city'],
                    'billing_country_id' => (!isset($inserted_data['address'])) ? 20 : 1,
                    'billing_zip' => $inserted_data['zipcode'],
                    'hear_about_us' => 'google',
                    'receive_offers' => 1,
                    'billing_phone' => $inserted_data['phone'],
                    'billing_fax' => $inserted_data['fax']]);

                $user = App\Model\User::create([
                    'first_name' => (isset($name[0])) ? $name[0] : '',
                    'last_name' => (isset($name[1])) ? $name[1] : '',
                    'active' => ($inserted_data['approved'] == 'Y') ? 1 : 0,
                    'updated_at' => date('Y-m-d', strtotime($approvedAt[0].' '.$approvedAt[1])),
                    'created_at' => date('Y-m-d', strtotime($createdAt[0].' '.$createdAt[1])),
                    'last_login' => date('Y-m-d', strtotime($lastLogin[0].' '.$lastLogin[1])),
                    'order_count' => $inserted_data['orders'],
                    'email' => $inserted_data['email'],
                    'password' => Hash::make($inserted_data['password']),
                    'role' => Role::$BUYER,
                    'buyer_meta_id' => $meta->id,
                ]);

                App\Model\BuyerShippingAddress::create([
                    'user_id' => $user->id,
                    'default' => 1,
                    'location' => (!isset($inserted_data['address'])) ? 'INT' : 'US',
                    'address' => $inserted_data['address'],
                    'unit' => $inserted_data['unit'],
                    'commercial' => 0,
                    'state_text' => $inserted_data['state'],
                    'country_id' => (!isset($inserted_data['address'])) ? 20 : 1,
                    'city' => $inserted_data['city'],
                    'zip' => $inserted_data['zipcode'],
                    'phone' => $inserted_data['phone'],
                    'fax' => $inserted_data['fax'],
                ]);

                $meta->user_id = $user->id;
                $meta->save();
            }

        }
        print_r($rowData);
    });
});

//Product Page
Route::get('/product/{slug}', 'HomeController@category_single_page')->name('product_single_page');
Route::post('/items/get/info', 'HomeController@get_items_info')->name('get_items_info');
Route::post('/get-matched-image', 'HomeController@get_matched_image')->name('get_matched_image');
Route::post('/get-matched-preorder-date', 'HomeController@get_matched_preorder_date')->name('get_matched_preorder_date');
Route::post('/color-name-filter', 'NewArrivalController@get_filter_color_name')->name('color_name_filter');

//category Page
// Route::get('/{category}', 'CategoryController@Category')->name('category_page');



 // Category Page
Route::get('/{category}', 'CategoryController@CategoryPage')->name('category_page');
Route::get('/{parent}/{category}', 'CategoryController@secondCategory')->name('second_category');
Route::get('/{parent}/{category}/{subcategory}', 'CategoryController@thirdCategory')->name('third_category');
Route::post('/items/get/sub_category', 'HomeController@getItemsSubCategory')->name('get_items_sub_category');
Route::post('/items/get/category', 'CategoryController@getItemsCategory')->name('get_items_category');
Route::get('/items/get/category_load', 'HomeController@get_items_category_load_ajax')->name('get_items_category_load_ajax');
Route::get('/items/get/sub_category_load', 'HomeController@get_items_sub_category_load_ajax')->name('get_items_sub_category_load_ajax');


