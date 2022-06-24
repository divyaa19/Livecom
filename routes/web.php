<?php

/** @var Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
// $router->get('productfrom', 'ProductController@create');
// $router->get('showproduct', 'ProductController@show');




use Laravel\Lumen\Routing\Router;

$router->get('/', function () use ($router) {
    return $router->app->version();
});
$router->get('/message', 'MessageController@sendMessaget');
$router->post('/message', 'MessageController@sendMessage');

$router->group(['middleware' => 'json'], function ($router) {
    $router->group(['middleware' => 'auth'], function ($router) {
        // $router->get('me', 'AuthController@me');
        $router->post('logout', 'AuthController@logout');
        $router->post('refresh', 'AuthController@refresh');



//// Rokon Uddin 2022-06-03 ///////

$router->post('addproduct', 'ProductController@store');
$router->post('updateproduct', 'ProductController@update');

$router->post('liveStore', 'LiveStoreController@store');
$router->post('updateLiveStore', 'LiveStoreController@update');
$router->post('liveStreamOnOff', 'LiveStoreController@liveStreamOnOff');

$router->post('livestoreProductParticipants', 'LivestoreProductParticipants@store');

$router->post('winnerParticipants', 'LivestoreProductParticipants@winnerParticipants');


/// end broadcast api////////











        // $router->post('seller/register', 'SellerRegisterController@SellerRegister');
        // $router->post('update/seller', 'SellerRegisterController@updateSeller');
        // $router->post('update/seller/profileimage', 'SellerDashboard@updatesellerprofileimage');

        $router->post('withdrawal/request', 'WithdrawalRequestController@RequestWithdraw');
        $router->post('add/bank/user', 'WithdrawalRequestController@addBankUser');

        $router->post('withdrawal_otp', 'WalletController@withdrawal_otp');
        $router->post('withdraw', 'WalletController@insert_withdrawal');
        // $router->post('withdrawal_otp', 'WalletController@withdrawal_otp');
        $router->post('withdrawal/{seller_unique_id}', 'WalletController@withdrawal');
        $router->post('update/bank/{id}', 'WalletController@updateBankDetails');
        $router->post('add/bank', 'WalletController@addBankDetails');
        $router->post('delete/bank', 'WalletController@deleteBankDetails');
        $router->get('get/wallet', 'WalletController@getWallet');
        $router->get('get/bank', 'WalletController@getBank');
        $router->get('get/withdrawDetails', 'WalletController@getWithdrawDetails');
        $router->get('get/follow/{unique_id}', 'FollowController@getFollow');
        // $router->post('transfer_otp', 'TransferController@transfer_otp');
        $router->post('transfer', 'TransferController@insert_transfer');
        $router->get('get/transferDetails', 'TransferController@getTransferDetails');
        $router->get('profile_url/{id}', 'ProfileController@getProfileImage');
        $router->get('get/seller_profile_url/{seller_id}', 'ProfileController@getSellerProfileImage');
        $router->get('get/seller_banner/{seller_id}', 'ProfileController@getSellerBanner');
        $router->get('get/seller_store_image/{seller_id}', 'ProfileController@getSellerStoreImage');
        $router->get('get/transaction_history', 'TransactionHistoryController@transaction_history');
        $router->get('get/transfer_out', 'TransactionHistoryController@transferOut');
        $router->get('get/transfer_in', 'TransactionHistoryController@transferIn');
        $router->get('get/reloadHistory', 'TransactionHistoryController@reloadHistory');
        $router->get('get/withdraw', 'TransactionHistoryController@withdrawHistory');
        $router->get('get/referral', 'TransactionHistoryController@referral_earnings');
        $router->get('get/livestore/e-commerce', 'TransactionHistoryController@eCommerceTransaction');
        $router->get('get/livestore/auction-low', 'TransactionHistoryController@auctionLowTransaction');
        $router->get('get/livestore/auction-high', 'TransactionHistoryController@auctionHighTransaction');
        $router->get('get/livestore/out-bid', 'TransactionHistoryController@outBidTransaction');
        $router->get('get/livestore/bid-fast', 'TransactionHistoryController@bidFastTransaction');
        $router->get('get/livestream/auction-high', 'TransactionHistoryController@auctionHigh_LiveTransaction');
        $router->get('get/livestream/auction-low', 'TransactionHistoryController@auctionLow_LiveTransaction');
        $router->get('get/livestream/eCommerce', 'TransactionHistoryController@eCommerce_LiveTransaction');
        $router->get('get/livestream/bid_fast', 'TransactionHistoryController@bidFast_LiveTransaction');
        $router->get('get/livestream/bid_return', 'TransactionHistoryController@return_bid_deposits');
        $router->get('get/livestream/forfeit_rate', 'TransactionHistoryController@forfeit_rate');
        $router->post('billplz', 'BillplzController@createBillplzCollection');
        $router->get('get/billplz/reload_detail', 'BillplzController@reloadDetails');
        $router->get('get/reload_bundle', 'BillplzController@ReloadBundles');
        $router->get('get/blocked_customer/{user_unique_id}', 'CustomerController@getCustomerBlockList');
        $router->post('get/block_customer', 'CustomerController@blockOrUnblock');
        $router->post('block_unfollow', 'CustomerController@blockAndUnfollow');
        $router->post('delete_store', 'CustomerController@deactivate_store');
        $router->post('follow/{unique_id}', 'CustomerController@followOrUnfollow');
        $router->post('switch', 'CustomerController@switch_account');
        $router->get('notification_count_member', 'CustomerController@notification_count_member');
        $router->get('notification_count_seller', 'CustomerController@notification_count_seller');

        $router->post('report/seller', 'ReportsController@reportseller');

        $router->get('get/member_info', 'CustomerController@getMemberInfo');

        $router->get('get/courier', 'CourierController@getCourier');


        //// Rokon Uddin 2022-06-03 ///////

        $router->post('addproduct', 'ProductController@store');
        $router->post('updateproduct', 'ProductController@update');

        $router->post('liveStore', 'LiveStoreController@store');
        $router->post('updateLiveStore', 'LiveStoreController@update');
        $router->post('liveStreamOnOff', 'LiveStoreController@liveStreamOnOff');

        $router->post('livestoreProductParticipants', 'LivestoreProductParticipants@store');

        $router->post('winnerParticipants', 'LivestoreProductParticipants@winnerParticipants');


        /// end broadcast api////////


        $router->get('chatroom', 'ChatController@getRoom');
        $router->get('get/chat/room/owner/{chatroom_id}', 'ChatController@getRoomOwnerByID');
        $router->get('get/chat/room/owner', 'ChatController@getRoomOwner');
        $router->get('chat/message/{chatroom_id}', 'ChatController@ChatMessage');
        $router->get('chat/exist/{user_id}', 'ChatController@checkIfRoomExist');
        $router->post('sent/message', 'ChatController@sentChat');
        $router->get('get/message', 'ChatController@getRoomMessage');
        $router->post('update/isread', 'ChatController@updateIsRead');
        $router->get('count/unread/{chatroom_id}', 'ChatController@countUnRead');

        

        $router->post('sent/notifications', 'NotificationsController@sentNotification');
        $router->get('get/notifications/{unique_id}', 'NotificationsController@getNotifications');
        $router->delete('delete/notifications/{notification_id}', 'NotificationsController@deleteNotification');


        // $router->get('get/sellerproduct', 'ProductController@getSellerproduct');
        // $router->post('seller/addproduct', 'ProductController@addsellerproduct');
        // $router->post('seller/updateproduct/{product_id}', 'ProductController@addsellerproduct');

        


        $router->post('search_user', 'TransferController@searchUser');
        $router->post('get/searchUser', 'TransferController@searchUser');
        $router->post('search_following', 'TransferController@searchFollowing');
        $router->post('search_store', 'TransferController@searchStore');
        $router->get('live_selling', 'HomeController@live_selling');
        $router->get('live_store', 'HomeController@live_store');
        $router->get('entertainment', 'HomeController@live_entertainment');
        $router->post('live_store_product', 'HomeController@live_store_product');
        $router->post('search_followers', 'TransferController@searchFollowers');
        $router->get('search_product', 'HomeController@searchProduct');
        $router->get('search_seller', 'HomeController@searchSeller');
        $router->get('search_stream', 'HomeController@searchStream');
        $router->get('search_member', 'HomeController@searchMember');


        // $router->get('seller/shop_activity/toship', 'ShopActivityController@getshopactivity_toship');
        // $router->get('seller/shop_activity/soldout', 'ShopActivityController@getshopactivity_soldout');
        // $router->get('seller/shop_activity/toremove', 'ShopActivityController@getshopactivity_toremove');

        // $router->post('seller/shop_activity/restock-product/{product_id}', 'ShopActivityController@restocktheproduct');
        // $router->get('seller/getremovedtheproduct', 'ShopActivityController@getremovedtheproduct');
        // $router->post('seller/removeproduct/{product_id}', 'ShopActivityController@removetheproduct');

        $router->get('get_banner', 'BannerController@getBanner');

        // $router->get('seller/shop_activity/toship', 'ShopActivityController@getshopactivity_toship');
        //$router->get('seller/shop_activity/soldout', 'ShopActivityController@getshopactivity_soldout');
        //$router->get('seller/shop_activity/toremove', 'ShopActivityController@getshopactivity_toremove');
        $router->post('seller/password-confirm', 'AccountSetting@updatepassword');
        //$router->delete('seller/bank-details/{account_number}', 'AccountSetting@deletebankaccount');
        //$router->post('seller/bank-details/{account_number}', 'AccountSetting@editbankaccount');
        //$router->get('seller/add-bank-details/{account_number}', 'AccountSetting@getbankaccount');
        //$router->post('seller/shop_activity/restock-product/{product_id}', 'ShopActivityController@restocktheproduct');
        //$router->get('seller/getremovedtheproduct', 'ShopActivityController@getremovedtheproduct');
        //$router->post('seller/removeproduct/{product_id}', 'ShopActivityController@removetheproduct');


        $router->post('home', 'HomeController@get_game_mode');
        $router->post('live_store', 'HomeController@live_store');
        $router->post('entertainment', 'HomeController@live_entertainment');
        $router->post('live_store_product', 'HomeController@live_store_product');
        $router->post('product', 'HomeController@product');
        $router->post('get/wallet', 'WalletController@getWallet');
        $router->get('get/catalog', 'SellerProfileController@sellerCatalog');
        $router->get('get/productinfo/{product_id}', 'SellerProfileController@productInfo');
        $router->get('get/playlist', 'SellerProfileController@sellerPlaylist');
        $router->get('get/follow_count', 'FollowController@followCount');
        $router->get('get/store_follow_count/{seller_unique_id}', 'FollowController@storeFollowCount');

        // $router->post('buy/product/{stream_id}','BuyProductController@productBuyNow');
        // $router->post('discount/product/{id}', 'BuyProductController@getDiscountPrice');
        $router->get('promotionDetails/{id}', 'BuyProductController@promotionDetails');
        $router->get('get/states', 'StatesController@getStates');
        $router->get('get/cities', 'CityController@getCity');
        $router->get('get/all_bank', 'BankController@getAllBank');
        $router->get('get/bank/{bank_id}', 'BankController@getBankById');
        $router->post('cancel/{order_list_id}', 'BuyProductController@cancelOrder');
        $router->get('cancel_details/{order_cancel_id}/{order_cart_list_id}', 'BuyProductController@cancelDetails');
        $router->get('re-order/{order_cart_list_id}', 'BuyProductController@reorder');

        $router->get('get/all/vod', 'VODController@getVOD');
        $router->get('get/vod/{unique_id}', 'VODController@getVODbyUnique_id');
        $router->post('like/{user_id}', 'VODController@likeorUnlike');
        $router->post('go_live', 'LiveController@go_live');
        $router->post('schedule_live', 'LiveController@schedule_live');
        $router->post('{live_id}/pin_message', 'LiveController@pin_message');
        $router->post('{live_id}/quick_add', 'LiveController@quickAdd');
        $router->post('end_stream/{live_id}', 'LiveController@endLiveStream');
        $router->post('start_selling/{product_id}', 'LiveController@startSelling');

        // $router->post('upload', 'UploadController@store');

    $router->get('get/sellerproduct', 'ProductController@getSellerproduct');
    $router->post('seller/addproduct', 'ProductController@addsellerproduct');
    $router->post('seller/updateproduct/{product_id}', 'ProductController@addsellerproduct');


    // $router->post('search_user', 'TransferController@searchUser');
    // $router->post('get/searchUser', 'TransferController@searchUser');
    // $router->post('search_following', 'TransferController@searchFollowing');
    // $router->post('search_store', 'TransferController@searchStore');
    // $router->get('live_selling', 'HomeController@live_selling');
    // $router->get('live_store', 'HomeController@live_store');
    // $router->get('entertainment', 'HomeController@live_entertainment');
    // $router->post('live_store_product', 'HomeController@live_store_product');
    // $router->post('search_followers', 'TransferController@searchFollowers');
    // $router->get('search_product', 'HomeController@searchProduct');
    // $router->get('search_seller', 'HomeController@searchSeller');
    // $router->get('search_stream', 'HomeController@searchStream');
    // $router->get('seller/shop_activity/toship', 'ShopActivityController@getshopactivity_toship');
    $router->get('seller/shop_activity/soldout', 'ShopActivityController@getshopactivity_soldout');
    $router->get('seller/shop_activity/toremove', 'ShopActivityController@getshopactivity_toremove');
    $router->delete('seller/bank-details/{account_number}', 'AccountSetting@deletebankaccount');
    $router->post('seller/bank-details/{account_number}', 'AccountSetting@editbankaccount');
    $router->get('seller/add-bank-details', 'AccountSetting@getbankaccount');
    $router->post('seller/shop_activity/restock-product/{product_id}', 'ShopActivityController@restocktheproduct');
    $router->get('seller/getremovedtheproduct', 'ShopActivityController@getremovedtheproduct');
    $router->post('seller/removeproduct/{product_id}', 'ShopActivityController@removetheproduct');
    $router->get('get_banner', 'BannerController@getBanner');
    $router->post('home', 'HomeController@get_game_mode');
    $router->post('live_store', 'HomeController@live_store');
    $router->post('entertainment', 'HomeController@live_entertainment');
    $router->post('live_store_product', 'HomeController@live_store_product');
    $router->post('product', 'HomeController@product');
    $router->post('get/wallet', 'WalletController@getWallet');
    $router->get('get/catalog', 'SellerProfileController@sellerCatalog');
    $router->get('get/catalog/{id}', 'SellerProfileController@productInfo');
    $router->get('get/playlist', 'SellerProfileController@sellerPlaylist');
    // $router->get('get/follow_count', 'SellerProfileController@followCount');
    $router->post('buy/product/{id}','BuyProductController@productBuyNow');
    $router->post('discount/product/{product_id}','BuyProductController@getDiscountPrice');
    $router->get('promotionDetails/{id}','BuyProductController@promotionDetails');
    $router->get('get/states','StatesController@getStates');
    $router->get('get/cities','CityController@getCity');
    $router->get('cancel/{order_id}','BuyProductController@cancelOrder');
    $router->post('paid/product/{id}','BuyProductController@orderPaid');
    $router->post('billplz/payment/{order_id}','BillplzController@createPaymentBill');
    $router->get('get/order_status', 'OrderStatusController@getOrderStatus');
    $router->post('add_cart/product/{product_id}','BuyProductController@addToCart');
    $router->get('my_cart','BuyProductController@myCart');
    $router->get('cart_checkout','BuyProductController@myCartCheckOut');

  
    $router->get('seller/detail/{unique_id}', 'ProfileController@getSellerProfile');
        // $router->get('cancel/{order_id}', 'BuyProductController@cancelOrder');
        $router->get('my_cart', 'BuyProductController@myCart');
        $router->post('cart_total', 'BuyProductController@myCartTotal');
        $router->get('cart_paid/{order_id}', 'BuyProductController@orderPaid');
        $router->get('cart_order_details/{order_cart_list_id}', 'BuyProductController@orderDetails');
        $router->post('add_order_cart/{product_id}', 'BuyProductController@addToOrderCart');
        $router->post('update-cart-price/{order_cart_id}', 'BuyProductController@updateCartPrice');
        // $router->get('add/product/to_pay/{cart_id}', 'BuyProductController@myCartToPay');
        $router->get('seller/getpromotion/{product_id}/{seller_id}', 'CapeginController@getpromotion');


        $router->get('get/product/review/{product_id}/{seller_unique_id}', 'ProductReviewController@getProductReview');
        $router->post('add/product/review/{order_cart_list_id}', 'ProductReviewController@addProductReview');
        $router->get('get/store_overall_review/{seller_unique_id}', 'ProductReviewController@getStore_Overall_Review');

    $router->get('seller/to_ship/{seller_unique_id}', 'ShopActivityController@get_to_ship');
    $router->get('seller/sold_out/{seller_unique_id}', 'ShopActivityController@get_sold_out');
    $router->get('seller/to_remove/{seller_unique_id}', 'ShopActivityController@get_to_remove');

    $router->get('get/bidders/{product_id}', 'AuctionHighController@getBidderList');

    $router->get('get/participant/{bid_session}', 'AuctionHighController@getParticipantCount');

        $router->get('get/member/dashboard', 'MemberDashboardController@getMemberInfo');
        $router->post('update/member/update_info', 'MemberDashboardController@updateInfo');
        $router->get('get/member/referral_link', 'MemberDashboardController@getReferralLink');

        $router->get('get/member/my_purchase/{user_id}', 'MemberDashboardController@memberMyPurchase');

        $router->get('get/return_reasons', 'ReturnController@getReturnReasons');
        $router->post('product/return/{order_cart_list_id}', 'ReturnController@productReturn');
        $router->get('get/my_cart/return_refund/{order_cart_list_id}', 'ReturnController@getMyCartReturnRefund');
        $router->get('get/return_refund_details/{return_id}', 'ReturnController@getReturnRefundDetails');


        $router->get('seller/detail/{unique_id}', 'ProfileController@getSellerProfile');


        //$router->post('seller/password-change', 'AccountSetting@password_request_otp');
        //$router->post('seller/add/delivery-address', 'AccountSetting@deliveryaddress');
        //$router->get('seller/delivery-address', 'AccountSetting@getdeliveryaddress');
        //$router->delete('seller/delivery-address/{address_id}', 'AccountSetting@deletedeliveryaddress');
        //$router->post('seller/update/delivery-address/{address_id}', 'AccountSetting@updatedeliveryaddress');

        //$router->post('seller/add-bank-details', 'AccountSetting@createbankaccount');
        //$router->post('seller/add-bank-details/otp', 'AccountSetting@otp_to_createbankaccount');
        //$router->delete('seller/bank-details/{account_number}', 'AccountSetting@deletebankaccount');
        //$router->post('seller/bank-details/{account_number}', 'AccountSetting@editbankaccount');
        //$router->get('seller/add-bank-details', 'AccountSetting@getbankaccount');


        $router->post('compare/password', 'AccountSetting@comparepassword');


        $router->post('seller/shop_activity/restock-product/{product_id}', 'ShopActivityController@restocktheproduct');
        $router->get('seller/getremovedtheproduct', 'ShopActivityController@getremovedtheproduct');
        $router->post('seller/removeproduct/{product_id}', 'ShopActivityController@removetheproduct');

        $router->get('get_banner', 'BannerController@getBanner');


        $router->post('api/otp', 'SmsController@send_sms');

        $router->post('password/reset-request', 'RequestPasswordController@sendResetLinkEmail');
        $router->post('password/reset', ['as' => 'password.reset', 'uses' => 'PasswordResetController@PasswordReset']);

        $router->post('home', 'HomeController@get_game_mode');

        $router->post('live_store', 'HomeController@live_store');

        $router->post('entertainment', 'HomeController@live_entertainment');

        $router->post('live_store_product', 'HomeController@live_store_product');

        $router->post('product', 'HomeController@product');

        // $router->post('seller/addproduct', 'ProductController@addSellerProduct');

        $router->post('seller/addproduct', 'ProductController@addsellerproduct');

        $router->post('bidder_list/auction_high', 'AuctionHighController@auctionHighBidderList');

        $router->post('bid_won/auction_high', 'AuctionHighController@bidWon');

        $router->get('latest_bid', 'AuctionHighController@getLatestBid');

        $router->get('get/live_store/e-commerce/{product_id}', 'LiveController@get_e_commerce_Product');

        $router->get('get/live_store/auction_low/{product_id}', 'LiveController@getALProduct');

        $router->get('get/product_listing/{seller_id}', 'MemberGameModeController@productListing');

        $router->post('participate/{product_id}', 'MemberGameModeController@addLiveStoreParticipants');

        $router->post('live_stream/add_participants', 'MemberGameModeController@addLiveStreamParticipants');

        $router->get('get/lucky_draw/{product_session_id}', 'LiveController@luckyDrawRoll');

        $router->get('get/live_stream_product_info', 'MemberGameModeController@liveStream_ProductInfo');

        $router->get('get/live_stream_product_info', 'MemberGameModeController@liveStream_ProductInfo');

        $router->post('bid/auction_high/{participant_id}', 'MemberGameModeController@livestore_BidNowButton');

        $router->get('get/store_rating', 'ProductReviewController@getStore_Overall_Review');

        $router->get('get/order_status', 'OrderStatusController@getOrderStatusList');

        $router->post('get/wallet', 'WalletController@getWallet');
    });


    $router->group(['prefix' => 'product'], function ($router) {
        $router->get('detail/{product_id}', 'ProductController@');
    });

    $router->get('getvoucher/{product_id}', 'VoucherController@getvoucher');
    $router->post('applyvoucher', 'VoucherController@applyvoucher');

    $router->get('get/seller_info', 'CustomerController@getSellerInfo');
    $router->get('get/seller_details/{seller_id}', 'CustomerController@getSellerDetails');
    $router->get('get/member_details/{user_unique_id}', 'CustomerController@getMemberDetails');

    $router->get('get_banner', 'BannerController@getBanner');
    $router->get('get/billplz/returnstatus', 'BillplzController@returnstatus'); //callback URL

    $router->post('request_otp', 'AuthController@request_otp');

    $router->post('register', 'AuthController@register');




    $router->post('login', 'AuthController@login');
    $router->get('/message1', 'MessageController@sendMessage1');
// $router->get('publish/{product_id}','AuctionHighController@getLatestBid');

    $router->get('auth/facebook', 'AuthController@redirectToFacebook');
    $router->get('auth/facebook/callback', 'AuthController@handleFacebookCallback');

    $router->get('auth/google', 'AuthController@redirectToGoogle');
    $router->get('auth/google/callback', 'AuthController@handleGoogleCallback');

    $router->get('auth/apple', 'AuthController@redirectToApple');
    $router->get('auth/apple/callback', 'AuthController@handleAppleCallback');


    $router->post('seller/capigen-addpromotion', 'CapeginController@addcapignbyseller');

    $router->get('get_blocked_list', 'SystemPageController@getblockedaccount');
    $router->post('api/otp', 'SmsController@send_sms');
    $router->post('password/reset-request', 'RequestPasswordController@sendResetLinkEmail');
    $router->post('password/reset', ['as' => 'password.reset', 'uses' => 'PasswordResetController@PasswordReset']);


$router->post('update/seller/profileimage/{seller_unique_id}', 'SellerDashboard@updatesellerprofileimage');
$router->post('update/seller/banner/{seller_unique_id}', 'SellerDashboard@updateSellerStoreBanner');
$router->post('update/seller/store_info/{seller_unique_id}', 'SellerDashboard@updateSellerInfo');
$router->post('update/seller/seller_store_info/{seller_unique_id}', 'SellerDashboard@updateSellerStoreInfo');
$router->get('seller/shop_summary/{seller_unique_id}', 'SellerDashboard@getshopSummary');
$router->get('seller/shop_available_balance/{seller_unique_id}', 'SellerDashboard@getAvailableBalance');

    $router->get('seller/capigen-addpromotion', 'CapeginController@getcapginbyseller');
    $router->post('api/otp', 'SmsController@send_sms');
    $router->post('password/reset-request', 'RequestPasswordController@sendResetLinkEmail');
    $router->post('password/reset', ['as' => 'password.reset', 'uses' => 'PasswordResetController@PasswordReset']);


    $router->post('seller/capigen-addpromotion', 'CapeginController@addcapignbyseller');
    $router->post('seller/capigen-editpromotion/{promotion_id}', 'CapeginController@editpromotion');
    $router->post('seller/capigen-endpromotion/{promotion_id}', 'CapeginController@endpromotion');
    $router->post('seller/capigen-duplicatepromotion/{promotion_id}', 'CapeginController@duplicatepromotion');
    $router->delete('seller/capigen-deletepromotion/{promotion_id}', 'CapeginController@deletepromotion');
    $router->get('seller/capigen-addpromotion', 'CapeginController@getcapginbyseller');
    $router->post('seller/search-promotion/{seller_unique_id}', 'CapeginController@searchCampaign');
    $router->post('join_campaign/{unique_id}', 'CapeginController@joinCampaign');
    $router->get('get/joined_campaign/{unique_id}', 'CapeginController@getJoinedCampaign');



    $router->get('get/sellerproduct', 'ProductController@getSellerproduct');
// $router->post('seller/addproduct', 'ProductController@addsellerproduct');
    $router->post('add/specifications/{product_id}', 'ProductController@addspecifications');
    $router->post('seller/editvariation/{product_id}/{product_variaion_id}', 'ProductController@editvariation');
    $router->post(
        'seller/editspecification/{product_id}/{product_specification_id}',
        'ProductController@editspecification'
    );
    $router->delete(
        'seller/deletespecification/{product_id}/{product_specification_id}',
        'ProductController@deletespecification'
    );
    $router->delete('seller/deletevariation/{product_id}/{product_variaion_id}', 'ProductController@deletevariation');
    $router->post('seller/updateproduct/{product_id}', 'ProductController@updatesellerproduct');

    $router->post('add/product/new', 'ProductController@addproduct');
    $router->get('get/product/new', 'ProductController@getproduct');
    $router->post('update/product/new/{product_id}', 'ProductController@updateproduct');
    $router->delete('delete/product/new/{product_id}', 'ProductController@deleteproduct');
    $router->post('delete/product/tohistory/{product_id}', 'ProductController@deletetohistory');
    $router->post('search-product/{seller_unique_id}', 'ProductController@searchProduct');

    $router->post('add/promotion/new/{seller_unique_id}', 'CapeginController@addpromotionbyseller');
    $router->get('get/promotion/new', 'CapeginController@getpromotionseller');
    $router->delete('delete/promotion/new/{discount_id}', 'CapeginController@deletepromotion');
    $router->post('update/status/endpromotion/{discount_id}', 'CapeginController@endpromotion');
    $router->post('duplicate/promotion/new/{discount_id}', 'CapeginController@duplicatepromotion');
    $router->post('edit/promotion/new/{promotion_id}', 'CapeginController@editpromotion');
    $router->get('get/promotion/foryou', 'CapeginController@getpromotionforyou');


    $router->get('getcourierid', 'StatusIdController@getcourierid');
    $router->get('order/status', 'StatusIdController@getstatus');
    $router->get('order/stage', 'StatusIdController@getstage');


    $router->delete('seller/bank-details/{account_number}', 'AccountSetting@deletebankaccount');
    $router->post('seller/bank-details/{account_number}', 'AccountSetting@editbankaccount');
    $router->get('seller/add-bank-details/{account_number}', 'AccountSetting@getbankaccount');


    // $router->post('update/seller/profileimage', 'SellerDashboard@updatesellerprofileimage');
    // $router->post('update/seller/store_info/{seller_unique_id}', 'SellerDashboard@updateSellerInfo');
    // $router->get('seller/shop_summary/{seller_unique_id}', 'SellerDashboard@getshopSummary');
    // $router->get('seller/shop_available_balance/{seller_unique_id}', 'SellerDashboard@getAvailableBalance');


    $router->post('seller/register', 'SellerRegisterController@SellerRegister');
    $router->post('update/seller', 'SellerRegisterController@updateSeller');
    $router->get('get/seller_info', 'CustomerController@getSellerInfo');

    $router->get('get/all/sellers/streams/{stream_type}', 'LiveController@getAllSellingStreams');
    $router->get('get/all/members/streams/{stream_type}', 'LiveController@getAllEntertainmentStreams');
    $router->get('get/all/streams', 'LiveController@getAllStreams');
    $router->get('get/stream/{stream_type}', 'LiveController@getStream');

    // $router->get('seller/shop_activity/toship', 'ShopActivityController@getshopactivity_toship');
    $router->get('seller/shop_activity/soldout/{store_id}', 'ShopActivityController@getshopactivity_soldout');
    $router->get('seller/shop_activity/toremove/{seller_unique_id}', 'ShopActivityController@getshopactivity_toremove');
    $router->post('seller/shop_activity/restock-product/{product_id}', 'ShopActivityController@restocktheproduct');
    $router->get('seller/getremovedtheproduct', 'ShopActivityController@getremovedtheproduct');
    $router->post('seller/removeproduct/{product_id}', 'ShopActivityController@removetheproduct');

    $router->get('get/sellerproduct', 'ProductController@getSellerproduct');
    $router->post('seller/addproduct', 'ProductController@addsellerproduct');
    $router->post('seller/updateproduct/{product_id}', 'ProductController@updatesellerproduct');

    $router->get('order/status', 'StatusIdController@getstatus');
    $router->get('order/stage', 'StatusIdController@getstage');
    $router->get('order/getcountry', 'StatusIdController@getcountry');
    $router->get('order/getzone', 'StatusIdController@getzone');

    $router->post('update-password', 'AccountSetting@updatePassword');
    $router->post('compare-password','AccountSetting@checkCurrentPassword');
    
    $router->delete('seller/bank-details/{account_number}', 'AccountSetting@deletebankaccount');
    $router->post('seller/bank-details/{account_number}', 'AccountSetting@editbankaccount');
    $router->get('seller/add-bank-details/{account_number}', 'AccountSetting@getbankaccount');
    $router->delete('seller/delete-product-permanently/{product_id}', 'AccountSetting@deleteremovedtheproduct');
    $router->post('seller/password-change', 'AccountSetting@password_request_otp');
    $router->post('seller/add/delivery-address', 'AccountSetting@deliveryaddress');
    $router->get('seller/delivery-address', 'AccountSetting@getdeliveryaddress');
    $router->delete('seller/delivery-address/{address_id}/{user_id}', 'AccountSetting@deletedeliveryaddress');
    $router->post('seller/update/delivery-address/{address_id}', 'AccountSetting@updatedeliveryaddress');
    $router->post('seller/add-bank-details', 'AccountSetting@createbankaccount');
    $router->post('seller/add-bank-details/otp', 'AccountSetting@otp_to_createbankaccount');


    $router->get('get/seller_info', 'CustomerController@getSellerInfo');

    $router->get('seller/shop_activity/toship/{seller_unique_id}', 'ShopActivityController@getshopactivity_toship');
    $router->get('seller/shop_activity/soldout', 'ShopActivityController@getshopactivity_soldout');
    $router->get('seller/shop_activity/toremove', 'ShopActivityController@getshopactivity_toremove');
    $router->post('seller/shop_activity/restock-product/{product_id}', 'ShopActivityController@restocktheproduct');
    $router->get('seller/getremovedtheproduct', 'ShopActivityController@getremovedtheproduct');
    $router->post('seller/removeproduct/{product_id}', 'ShopActivityController@removetheproduct');
    $router->post('seller/trackingnumber/{customer_id}', 'ShopActivityController@trackingnumber');

    $router->get('get/seller/sold/{product_id}', 'SalesController@getSoldAmount');

    $router->post('api/otp', 'SmsController@send_sms');
    $router->post('password/reset-request', 'RequestPasswordController@sendResetLinkEmail');
    $router->post('password/reset', ['as' => 'password.reset', 'uses' => 'PasswordResetController@PasswordReset']);


    $router->get('get/getlivestreamSellerproduct', 'ProductController@getlivestreamSellerproduct');


    $router->post('seller/startlivestreambyseller', 'Livestream@startstreambysellersession');
    $router->post('seller/addlivestreambyseller', 'Livestream@addstreambyseller');


    $router->post('search', 'SearchController@search');
    $router->post('seller/endsession', 'Livestream@endsession');
    $router->post('seller/adduserbid', 'Livestream@customerupdate');
    $router->post('seller/customerwinnerupdate', 'SearchController@customerwinnerupdate');

    $router->get('getcustomerupdate', 'Livestream@getcustomerupdate');


    $router->post('seller/endlivestreambyseller', 'Livestream@endsession');
    $router->group(['prefix' => 'cities'], function ($router) {
        $router->get('/', 'CityController@all');
        $router->get('{state_id}', 'CityController@getCity');
    });

    $router->group(['prefix' => 'states'], function ($router) {
        $router->get('/', 'StatesController@getStates');
    });

    $router->group(['prefix' => 'products'], function ($router) {
        $router->get('detail/{product_id}', 'ProductController@detail');
        $router->get('/all/{seller_id}', 'ProductController@all');
        $router->get('/all/livestream/{seller_id}', 'ProductController@allStreamProduct');
        $router->get('/all/livestore/{seller_id}', 'ProductController@allStoreProduct');
        $router->get('/all', 'ProductController@home');
    });

    $router->group(['prefix' => 'streams'], function ($router) {
        $router->get('/all/{seller_id}', 'LiveController@getStreamProduct');
    });

    $router->group(['prefix' => 'categories'], function ($router) {
        $router->get('/', 'CategoryController@all');
        $router->get('/categoryid/{category_id}', 'CategoryController@findCategoryId');
    });
});
$router->post('update/member/profile_image', 'MemberDashboardController@updateProfileImage');
$router->post('upload/file', 'FileController@uploadFile');
$router->post('upload/vod/{unique_id}', 'FileController@uploadVideo');
