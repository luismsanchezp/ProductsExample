<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\api\v1\ProductController;
use App\Http\Controllers\api\v1\UserController;

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

Route::get('v1/users',
    [App\Http\Controllers\api\v1\UserController::class,'index'])->name('users.index');
Route::get('v1/users/{user}',
    [App\Http\Controllers\api\v1\UserController::class,'show'])->name('users.show');
Route::post('v1/users',
    [App\Http\Controllers\api\v1\UserController::class,'store'])->name('users.post');

Route::get('v1/users/{user}/products',
    [App\Http\Controllers\api\v1\ProductController::class,'index'])->name('products.index');
Route::get('v1/users/{user}/products/{product}',
    [App\Http\Controllers\api\v1\ProductController::class,'show'])->name('products.show');

Route::post('/v1/login',
    [App\Http\Controllers\api\v1\AuthController::class,
        'login'])->name('api.login');

Route::middleware(['auth:sanctum'])->group(function() {
    Route::post('/v1/logout',
        [App\Http\Controllers\api\v1\AuthController::class,
            'logout'])->name('api.logout');

    Route::put('v1/users/{user}',
        [App\Http\Controllers\api\v1\UserController::class,'update'])->name('users.update');
    Route::delete('v1/users/{user}',
        [App\Http\Controllers\api\v1\UserController::class,'destroy'])->name('users.destroy');

    Route::post('v1/users/{user}/products',
        [App\Http\Controllers\api\v1\ProductController::class,'store'])->name('products.post');
    Route::put('v1/users/{user}/products/{product}',
        [App\Http\Controllers\api\v1\ProductController::class,'update'])->name('products.update');
    Route::delete('v1/users/{user}/products/{product}',
        [App\Http\Controllers\api\v1\ProductController::class,'destroy'])->name('products.destroy');
});
