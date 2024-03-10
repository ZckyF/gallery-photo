<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\CommentPhotoController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\LikePhotoController;
use App\Http\Controllers\NewPasswordController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\ReportPhotoController;
use App\Http\Controllers\SaveController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware(['auth:sanctum'])->group(function () {
    // Route untuk user
    Route::resource('/users', UserController::class);
    Route::post('/reset-password',[ UserController::class,'resetPassword']);

    // Route untuk folder
    Route::resource('/folders', FolderController::class);
    // Route::get('/folders', [FolderController::class,'index']);
    // Route::post('/folders', [FolderController::class,'store']);
    // Route::delete('/folders/{folder}', [FolderController::class,'destroy']);
    // Route::patch('/folders/{folder}', [FolderController::class,'update']);

    // Route untuk photo
    Route::resource('/photos', PhotoController::class);
    // Route::get('/photos', [PhotoController::class,'index']);
    // Route::post('/photos', [PhotoController::class,'store']);
    // Route::delete('/photos/{photo}', [PhotoController::class,'destroy']);
    // Route untuk photo
    Route::resource('/like-photos', LikePhotoController::class);
    // Route::post('/likes',[LikeController::class,'store']);   
    
    // Route untuk save
    Route::resource('/saves', SaveController::class);
    // Route untuk report photo
    Route::resource('/report-photos', ReportPhotoController::class);
    // Route untuk comment photo
    Route::resource('/comment-photos', CommentPhotoController::class);
    // Route untuk follow
    Route::resource('/follows', FollowController::class);
    Route::delete('/follows-delete-by-id/{id}', [FollowController::class,'destroyById']);

    // Route untuk logout
    Route::get('/logout',[AuthenticationController::class,'logout']);
});
// Route untuk user
Route::get('/user-detail/{username}', [UserController::class, 'show']);
Route::get('/search/{keyword}/users', [UserController::class,'searchUser']);
// Route untuk leaderboard
Route::get('/leaderboard/followers', [LeaderboardController::class,'leaderboardFollower']);
// Route untuk follow
Route::get('/follows/{username}/detail', [FollowController::class, 'indexByUsername']);

// Route untuk photo
Route::get('/user/{username}/photos', [PhotoController::class, 'indexByUsername']);
Route::get('/random-photo', [PhotoController::class, 'randomPhoto']);
Route::get('/random-photo-hero', [PhotoController::class, 'randomPhotoHero']);
Route::get('/random-photo-following', [PhotoController::class, 'randomPhotoFollowing']);
Route::get('/detail-photo/{titleAndFileName}', [PhotoController::class,'show']);
// Similiar photo
Route::get('/similiar-photo/{titleAndFileName}', [PhotoController::class,'similiarPhoto']);
Route::get('/search/{keyword}/photos', [PhotoController::class,'searchPhoto']);
// Route untuk folder
Route::get('/user/{username}/folders', [FolderController::class,'indexByUsername']);
Route::get('/detail-folder/{usernameAndFolderName}', [FolderController::class,'show']);
Route::get('/search/{keyword}/folders', [FolderController::class,'searchFolder']);
// Route untuk like photo
Route::get('/photo/{idPhoto}/likes', [LikePhotoController::class, 'indexByIdPhoto']);
Route::get('/user/{username}/likes', [SaveController::class, 'indexByUsername']);

// Route untuk save photo
Route::get('/photo/{idPhoto}/saves', [SaveController::class, 'indexByIdPhoto']);
Route::get('/user/{username}/saves', [SaveController::class, 'indexByUsername']);
// Route untuk comment photo
Route::get('/photo/{idPhoto}/comments', [CommentPhotoController::class, 'indexByIdPhoto']);
// Route untuk login
Route::post('/login',[AuthenticationController::class,'login']);
// Route untuk register
Route::post('/join',[AuthenticationController::class,'join']);
// Route untuk verify otp
Route::post('/verify-otp',[AuthenticationController::class,'verifyOtp']);
Route::post('/verify-otp-join',[AuthenticationController::class,'verifyOtpJoin']);
// Route untuk resend-otp
Route::post('/resend-otp',[AuthenticationController::class,'resendOtp']);
// Route untuk send link forgot password
Route::post('/forgot-password', [NewPasswordController::class,'sendResetLinkEmail']);
Route::post('/reset-password', [NewPasswordController::class,'resetPassword']);
