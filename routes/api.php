<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::group(['middleware' => ['api'], 'namespace'=>'Api'], function () {

    Route::post('guest-token', 'AuthController@guestUserToken');
    Route::post('register', 'AuthController@register');
    Route::post('is-email', 'AuthController@isEmail');
  //  Route::post('register2', 'AuthController@register2');
    Route::post('sync-contacts', 'AuthController@sync_contacts');


    Route::post('login', 'AuthController@login');
    Route::post('social-fb', 'AuthController@fbLogin');
    Route::post('social-twitter', 'AuthController@twtLogin');
    Route::post('social-google/{id?}', 'AuthController@googleLogin');
    Route::post('renew-token', 'AuthController@renewToken');
    Route::post('forgot-password', 'AuthController@forgotPassword');

   // Route::post('forgot-password-phone', 'AuthController@forgotPasswordPhone');

    Route::post('forgot-password-phone', 'AuthController@ForgetPasswordPhone');
    Route::post('verify-forgot-password-code', 'AuthController@verifyForgotPasswordCode');
    Route::post('reset-password', 'AuthController@resetPassword');
    Route::post('username-availability', 'AuthController@checkUsernameAvailability');

    //Route::post('social-login', 'AuthController@socialLogin');

    // APP USER
    Route::group(['middleware' => 'authJWT'], function () {
        // REGISTER PROCESS  ForgetPasswordPhone
        Route::post('username-availability', 'AuthController@checkUsernameAvailability');
        Route::post('email-availability', 'AuthController@checkEmailAvailability');
        Route::patch('register-2', ['uses' =>'ProfileController@updateUser', 'as'=>'withUsername']);
        Route::post('send-verification-code', 'AuthController@sendVerificationCode');
        Route::post('resend-verification-code', 'AuthController@resendCode');
        Route::post('verify-code', 'AuthController@verifyCode');


        Route::post('logout', 'AuthController@logout');

        // MY PROFILE
        Route::get('user', 'ProfileController@getUserDetails');
        Route::patch('notification-status', 'ProfileController@updateNotificationStatus');
        Route::patch('device-info', 'ProfileController@updateDeviceToken');
        Route::post('change-password', 'ProfileController@changePassword');
        Route::patch('user', 'ProfileController@updateUser');

        // List CMS Pages
        Route::resource('cms-pages', 'CmsPageController', ['only'=>['index','show']]);
        // Create Contact Form
        Route::post('contact-forms', 'ContactFormController@store');
        // List Packages
        Route::resource('packages', 'PackageController', ['only'=>['index','show']]);
        // List Stream Categories
        Route::resource('stream-categories', 'StreamCategoryController', ['only'=>['index','show']]);
        // List and Create Stream
        // TODO: Add Indexing for Favorite / Watch Later / Saved streams
        Route::resource('streams', 'UserStreamController', ['only'=>['index','store']]);
        Route::post('streamsactions', 'UserStreamController@StreamActions');
        // TODO: Add Bulk Like / Disklike actions.
        Route::resource('stream-actions', 'StreamUserActionController', ['only'=>['index','store','destroy']]);

        Route::post('streams/{id}', 'UserStreamController@endStream');
        Route::get('streams/{type}', 'UserStreamController@indexByType');
        Route::get('find-user-stream', 'UserStreamController@indexByUserID'); // update
        Route::get('search-stream','UserStreamController@searchStream'); // update
        Route::get('stream-filter','UserStreamController@stream_filter');// update
        Route::get('popular-streams','UserStreamController@popular_streams');// update
        Route::post('remove-useractions','StreamUserActionController@delete_byType'); // update
        
        
        Route::resource('friend-requests', 'UserFriendRequestController', ['only'=>['index','store','destroy']]);
        Route::post('friend-requests/accept/{id}', 'UserFriendRequestController@acceptRequest');
        Route::post('friend-requests/reject/{id}', 'UserFriendRequestController@rejectRequest');
        Route::post('user-details', 'ProfileController@user_details'); // update
        Route::get('friend-list','FriendActionController@user_friend_list'); //update
        Route::post('search-friend','FriendActionController@search_friend'); //update
        Route::get('following-users/','FriendActionController@get_following_users'); //update
        Route::get('followed-users/','FriendActionController@get_followed_users'); //update
        Route::post('unfriend-users/','FriendActionController@unfriend_user'); //update
        Route::post('user-status','UserStreamController@checkUserStatus');// update for user status not actual service
        
        Route::post('follow-user','FriendActionController@user_follow');//update
        Route::post('unfollow-user','FriendActionController@unfollow_user'); //update
        Route::post('block-user/','FriendActionController@block_user'); //update
        Route::post('unblock-user/','FriendActionController@unblock_user'); //update
        Route::get('get-blocked-user','FriendActionController@get_blocked_users');// update

        Route::post('delete-user','ProfileController@delete_user'); //update
        Route::post('privacy-setting','ProfileController@privacy_setting'); //update
        
        
        
        
        
        Route::post('user-devices','ProfileController@getUserDevices'); //update
        Route::post('iosnotification','ProfileController@ios_notification'); //update
        Route::post('test','UserFriendRequestController@notification_test'); //update
    });


    // ADMIN USER
    Route::group(['middleware' => 'AuthAdminJWT'], function () {
        Route::resource('cms-pages', 'CmsPageController', ['except'=>['index','show']]);
        Route::resource('contact-forms', 'ContactFormController', ['except'=>['store']]);
        Route::resource('roles', 'RoleController');
        Route::resource('packages', 'PackageController', ['except'=>['index','show']]);
        Route::resource('permissions', 'PermissionController');
        Route::resource('stream-categories', 'StreamCategoryController', ['except'=>['index','show']]);
    });
});
