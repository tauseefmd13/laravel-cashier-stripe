<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriptionController;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::group(['middleware' => 'auth'], function () {

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('/subscriptions/create', [SubscriptionController::class, 'create'])->name('subscriptions.create');
    Route::post('/subscriptions/store', [SubscriptionController::class, 'store'])->name('subscriptions.store');
    Route::get('/subscriptions/{stripe_id}/show', [SubscriptionController::class, 'show'])->name('subscriptions.show');
    Route::put('/subscriptions/{stripe_id}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    Route::put('/subscriptions/{stripe_id}/resume', [SubscriptionController::class, 'resume'])->name('subscriptions.resume');
});
