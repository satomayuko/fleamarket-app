<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// 認証メール内リンクを踏んだとき（認証完了ハンドラ）
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // email_verified_at を更新
    return redirect()->route('mypage.profile.edit'); // 認証直後にプロフィール編集へ
})->middleware(['auth', 'signed'])->name('verification.verify');

// 認証メールの再送
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');

    Route::post('/items/{item}/like',  [FavoriteController::class, 'store'])->name('items.like');
    Route::delete('/items/{item}/like', [FavoriteController::class, 'destroy'])->name('items.unlike');

    Route::get('/mypage', [ProfileController::class, 'show'])->name('mypage');
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('mypage.profile.edit');
    Route::patch('/mypage/profile', [ProfileController::class, 'update'])->name('mypage.profile.update');
   
});
