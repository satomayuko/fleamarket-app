<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [ItemController::class, 'index'])->name('items.index');

Route::get('/items/mylist', [ItemController::class, 'indexMyList'])
    ->name('items.mylist')
    ->middleware('auth');

Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect()->route('mypage.profile.edit');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/sell', [ItemController::class, 'create'])->name('items.create');

    Route::post('/items', [ItemController::class, 'store'])->name('items.store');

    Route::post('/items/{item}/like', [FavoriteController::class, 'store'])->name('items.like');
    Route::delete('/items/{item}/like', [FavoriteController::class, 'destroy'])->name('items.unlike');

    Route::get('/mypage', [ProfileController::class, 'show'])->name('mypage');
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('mypage.profile.edit');
    Route::patch('/mypage/profile', [ProfileController::class, 'update'])->name('mypage.profile.update');

    Route::post('/items/{item}/comments', [CommentController::class, 'store'])->name('comments.store');

    Route::get('/purchase/{item}', [OrderController::class, 'confirm'])->name('orders.confirm');
    Route::post('/purchase/{item}/checkout', [OrderController::class, 'checkout'])->name('orders.checkout');
    Route::get('/purchase/{item}/success', [OrderController::class, 'success'])->name('orders.success');

    Route::get('/purchase/{item}/address', [AddressController::class, 'edit'])->name('orders.address');
    Route::post('/purchase/{item}/address', [AddressController::class, 'update'])->name('orders.address.update');
});
