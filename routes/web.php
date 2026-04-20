<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminPageController;
use App\Http\Controllers\Admin\AdminUserController;
//use App\Http\Controllers\Admin\AdminFormController;
use App\Http\Controllers\Admin\CompanyController;



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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/privacy-policy', function () {
    return view('privacy-policy');
})->name('privacy-policy');

Route::get('/terms-of-service', function () {
    return view('terms-of-service');
})->name('terms-of-service');








Route::prefix('admin')->group(function () 
{
    
    Route::get('/optimize', function () {
    // Run various optimization commands
    Artisan::call('optimize:clear');  // Clear cached views, routes, etc.
    Artisan::call('route:cache');     // Cache routes
    Artisan::call('config:cache');    // Cache config files
    Artisan::call('view:cache');      // Cache views
    
    return "Optimization commands have been executed.";
});

Route::get('/', [AdminAuthController::class, 'index']);
Route::get('login', [AdminAuthController::class, 'index'])->name('admin.login');
Route::post('admin-login', [AdminAuthController::class, 'postLogin'])->name('admin.login.post');

Route::group(['middleware' => 'admin'], function () {
    
   /******************************************new api**************************************************************/ 
   //  Route::post('/user/status/update', [AdminPageController::class, 'updateStatus'])->name('admin.user.status.update');
    
       /******************************************new api**************************************************************/ 
     //  Route::get('edit_profile', [AdminPageController::class, 'edit_profile'])->name('edit_profile');
    //   Route::post('update_profile', [AdminPageController::class, 'update_profile'])->name('update_profile');  
     
    
        Route::get('dashboard', [AdminPageController::class, 'dashboard'])->name('admin.dashboard');
         Route::get('dashboardData', [AdminPageController::class, 'dashboardData'])->name('admin.dashboardData');
         Route::post('/admin/profile-update', [AdminPageController::class, 'updateSettings'])->name('admin.profile.update');
        
        
          Route::get('add_driver', [AdminPageController::class, 'add_driver'])->name('admin.add_driver');
        Route::post('driver/driver_store', [AdminPageController::class, 'driver_store'])->name('admin.driver_store');
        Route::get('drivers', [AdminPageController::class, 'drivers'])->name('admin.drivers');
        Route::get('driver_details/{id?}', [AdminPageController::class, 'driver_details'])->name('admin.driver_details');
        Route::get('approvals', [AdminPageController::class, 'approvals'])->name('admin.approvals');
        Route::get('approval_details/{id}', [AdminPageController::class, 'approval_details'])->name('admin.approval_details');
        Route::get('announcements', [AdminPageController::class, 'announcements'])->name('admin.announcements');
        Route::get('add_announcements', [AdminPageController::class, 'add_announcements'])->name('admin.add_announcements');
        Route::post('announcements/store', [AdminPageController::class, 'storeAnnouncement'])->name('admin.announcements.store');
        Route::post('announcements/add_dash_announcement', [AdminPageController::class, 'add_dash_announcement'])->name('admin.add_dash_announcement.store');
        Route::put('announcement/update_announcements/{id}', [AdminPageController::class, 'update_announcements'])->name('admin.update_announcements');
        Route::get('announcement/edit_announcement/{id}/edit', [AdminPageController::class, 'edit_announcement'])->name('admin.edit_announcement');
        Route::post('announcement/delete_announcement/{id}', [AdminPageController::class, 'delete_announcement'])->name('admin.delete_announcement');
        
        Route::get('offer', [AdminPageController::class, 'offer'])->name('admin.offer');
        Route::get('add_offer', [AdminPageController::class, 'add_offer'])->name('admin.add_offer');
        Route::post('admin/offers/store', [AdminPageController::class, 'storeOffer'])->name('admin.offer.store');
        Route::get('/admin/search-driver', [AdminPageController::class, 'searchDriver'])->name('search.driver');
        // Show edit form
        Route::get('offer/edit_offer/{id}/edit', [AdminPageController::class, 'edit_offer'])->name('admin.edit_offer');
        Route::put('offer/update_offer/{id}', [AdminPageController::class, 'update_offer'])->name('admin.update_offer');
          Route::post('offer/delete_offer/{id}', [AdminPageController::class, 'delete_offer'])->name('admin.delete_offer');
        
        
        
       /*---------------------------------------------------------------------------------------------------------*/ 
        
        Route::get('reports', [AdminPageController::class, 'reports'])->name('admin.reports');
        Route::get('settings', [AdminPageController::class, 'settings'])->name('admin.settings');
        Route::get('logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
        
        Route::post('admin/update_approval_status/{id}', [AdminPageController::class, 'updateApprovalStatus'])->name('admin.update_approval_status');
        

         Route::post('admin/add_training_offer', [AdminPageController::class, 'add_training_offer'])->name('admin.add_training_offer');
          Route::post('saveMapSetting', [AdminPageController::class, 'saveMapSetting'])->name('admin.map.setting');
          
          
          Route::get('sub_admin', [AdminPageController::class, 'sub_admin'])->name('admin.sub_admin');
          Route::get('add_sub_admin', [AdminPageController::class, 'add_sub_admin'])->name('admin.add_sub_admin');
          Route::post('admin/store_sub_admin', [AdminPageController::class, 'store_sub_admin'])->name('admin.store_sub_admin');
          Route::get('admin/view/{id}', [AdminPageController::class, 'view_admin'])->name('admin.view');
          
         
         
});




});








