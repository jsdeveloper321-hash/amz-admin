<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
//use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\AuthController;

//use App\Http\Controllers\Api\Automotive\Cars\CarController;
use App\Http\Controllers\Api\CommonController;
//use App\Http\Controllers\Api\ChatController;
//use App\Http\Controllers\Api\FavouriteController;
//use App\Http\Controllers\Api\HotelController;
//use App\Http\Controllers\Api\BookingController;
//use App\Http\Controllers\Api\Admincontroller;
//use App\Http\Controllers\PayPalController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\Companycontroller;
use App\Http\Controllers\Api\ShiftController;

/*

|--------------------------------------------------------------------------

| API Routes

|--------------------------------------------------------------------------

|

| Here is where you can register API routes for your application. These

| routes are loaded by the RouteServiceProvider and all of them will

| be assigned to the "api" middleware group. Make something great!

|

*/
Route::prefix('auth')->group(function () {
    Route::post('signup', [AuthController::class, 'signup']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']); 
    Route::post('forgotPassword', [AuthController::class, 'forgotPassword']); 
    Route::post('createNewPassword', [AuthController::class, 'createNewPassword']); 
    Route::post('changePassword', [AuthController::class, 'changePassword']); 
    Route::post('get-profile', [AuthController::class, 'getProfile']);
    Route::post('update-profile', [AuthController::class, 'updateProfile']);
    
    
    
  //  Route::post('face_lock_status', [AuthController::class, 'face_lock_status']);
 //   Route::post('setAppPasscode', [AuthController::class, 'setAppPasscode']);   
   // Route::post('verifyAppPasscode', [AuthController::class, 'verifyAppPasscode']);  
     
     
     
     
  //   Route::post('password-reset', [AuthController::class, 'passwordReset']);
  
   
   // Route::post('create-new-password', [AuthController::class, 'createNewPassword']);
    //Route::post('change-password', [AuthController::class, 'changePassword']);
    // Route::post('create-new-password-without-login', [AuthController::class, 'createNewPasswordWithoutLogin']);
    //Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    
  });




Route::prefix('user')->group(function () {
            Route::get('get_rating', [UserController::class, 'get_rating']);
            Route::post('add_duty_request', [UserController::class, 'add_duty_request']);
            Route::post('change_duty', [UserController::class, 'change_duty']);   
            Route::post('duty_percentage', [UserController::class, 'duty_percentage']);  
            Route::post('duty_log_summary', [UserController::class, 'duty_log_summary']); 
            Route::post('get_current_duty_request', [UserController::class, 'get_current_duty_request']);
            Route::post('add_duty_video', [UserController::class, 'add_duty_video']);
            Route::post('get_duty_videos', [UserController::class, 'get_duty_videos']);
            Route::post('get_nearby_users', [UserController::class, 'get_nearby_users']);
            Route::get('check', [UserController::class, 'check']); 
            Route::post('get_announcements', [UserController::class, 'get_announcements']); 
            Route::post('get_offers', [UserController::class, 'get_offers']);  
             
         });





Route::prefix('chat')->group(function () {
     Route::post('get_employee_code_by_users', [ChatController::class, 'get_employee_code_by_users']);
     Route::post('insert_chat', [ChatController::class, 'insert_chat']);
     Route::post('get_chat', [ChatController::class, 'get_chat']);
     Route::post('get_last_messages', [ChatController::class, 'get_last_messages']);
});

/*Route::prefix('home')->group(function () {
    Route::post('get-home', [HomeController::class, 'getHome']);
    Route::get('get-privacy-policy', [CommonController::class, 'get_privacy_policy']);
    Route::get('get-terms-and-conditions', [CommonController::class, 'get_terms_and_condition']);
});
*/




Route::prefix('common')->group(function () {
    
    Route::get('get_terms_and_condition', [CommonController::class, 'get_terms_and_condition']);
    Route::get('get_about_us', [CommonController::class, 'get_about_us']);
    Route::get('get_faqs', [CommonController::class, 'get_faqs']);
    Route::get('get_privacy_policy', [CommonController::class, 'get_privacy_policy']);
    Route::get('get_support', [CommonController::class, 'get_support']);
    Route::post('ask_support', [CommonController::class, 'ask_support']);
    Route::post('get_sub_category_by_category_id', [CommonController::class, 'get_sub_category_by_category_id']);
    Route::get('get-payment', [CommonController::class, 'get_payment']);
    
});







Route::get('get_success_url', [PayPalController::class, 'successUrl'])->name('paypal.successUrl');

Route::get('add_payment_status_check', [PayPalController::class, 'add_payment_status_check'])->name('paypal.add_payment_status_check');

Route::get('/paypal/create-order', [PayPalController::class, 'createOrder'])->name('paypal.create');
Route::get('/paypal/success', [PayPalController::class, 'captureOrder'])->name('paypal.success');
Route::get('/paypal/cancel', function () {
     return redirect()->away('https://server-php-8-3.technorizen.com/cancel');
})->name('paypal.cancel');









