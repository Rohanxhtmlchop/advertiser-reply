<?php

use App\Http\Controllers\JsonInsertData\JsonInsertDataController;
use App\Http\Controllers\Campaign\CampaignController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Deal\DealController;
use App\Http\Controllers\Forgotpassword\ForgotpasswordController;
use App\Http\Controllers\Login\LoginController;
use App\Http\Controllers\Logout\LogoutController;
use App\Http\Controllers\NoPage\NoPageController;
use App\Http\Controllers\Registration\RegistrationController;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

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

/*Route::get('/', function () {
    return view('welcome');
});
*/
/* Dashboard */
Route::get('/',[ DashboardController::class,'index' ])->name('dashboard')->middleware('auth');
Route::post('/deal-dashboard',[ DashboardController::class, 'dashboardDealFilter'])->name('dashboard.dealfilter');
Route::post('/advertiser-dashboard',[ DashboardController::class, 'dashboardAdvertiserFilter'])->name('dashboard.advertiserfilter');

/* Login */
Route::get('/login',[ LoginController::class, 'index'])->name('login');
Route::post('/post-login',[ LoginController::class, 'postLogin'])->name('login.post');
Route::post('/post-resend-otp',[ LoginController::class, 'postReSendOTP'])->name('login.resend.otp');
Route::post('/post-verify-otp',[ LoginController::class, 'postVerifyOTP'])->name('login.verify.otp');
Route::post('/post-login-user',[ LoginController::class, 'postLoginUser'])->name('login.postuser');

/* Logout */
Route::get('/logout',[ LogoutController::class, 'index'])->name('logout');
Route::post('/post-remove-cookie-session',[ LogoutController::class, 'postRemoveSession'])->name('postremovesession');
//Route::get('/login/{id}',[ LoginController::class, 'loginAccessToken'])->name('login.accesstoken');

/* Registration */
Route::get('/registration',[ RegistrationController::class, 'index'])->name('registration');
Route::post('post-registration',[ RegistrationController::class, 'postRegistration'])->name('registration.post');

/* Forgot Password */
Route::get('/changepassword',[ForgotpasswordController::class, 'index'])->name('forgotpassword')->middleware('auth');
Route::post('/currentpassword',[ForgotpasswordController::class, 'currentPassword'])->name('forgotpassword.currentpassword');
Route::post('/post-changepassword',[ForgotpasswordController::class, 'postChangepassword'])->name('forgotpassword.post');
Route::post('/check-changepassword',[ForgotpasswordController::class, 'postCheckChangepassword'])->name('check.forgotpassword.post');

/* Deal */
Route::get('/deal',[DealController::class, 'index'])->name('deal')->middleware('auth');
Route::post('/post-deal-status',[DealController::class, 'postStatus'])->name('deal.status')->middleware('auth');

/* Campaign */
Route::get('/campaign',[CampaignController::class, 'index'])->name('campaign')->middleware('auth'); 
Route::post('/post-campaign-status',[CampaignController::class, 'postStatus'])->name('campaign.status')->middleware('auth');
Route::post('/get-campaign-detail',[CampaignController::class, 'getEditCampaignDetail'])->name('campaign.editdetail')->middleware('auth'); 

/* Campaign Edit */
Route::get('/campaign/edit/{id}',[CampaignController::class, 'getEditCampaignInfo'])->name('campaign.edit')->middleware('auth');
Route::post('/campaign/post-campaign-edit',[CampaignController::class, 'postEditCampaign'])->name('post.campaign.edit')->middleware('auth'); 

/* No page */
Route::get('/no-page',[NoPageController::class, 'index'])->name('nopage')->middleware('auth');

/* No page */
Route::get('/api-insert',[JsonInsertDataController::class, 'index'])->name('api-insert')->middleware('auth'); 
Route::post('/get-json-data',[JsonInsertDataController::class, 'getJSONData'])->name('getjsondata')->middleware('auth'); 
Route::post('/json-mapping-data',[JsonInsertDataController::class, 'jsonMappingData'])->name('jsonMappingData')->middleware('auth'); 
Route::post('/insert-json-data',[JsonInsertDataController::class, 'InsertjsonData'])->name('insertjsonData')->middleware('auth'); 
