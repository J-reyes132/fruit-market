<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;

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
Route::post('v1/login', [AuthController::class, 'login'])->name('login');
Route::post('v1/register', [AuthController::class, 'register'])->name('register');


Route::group(['middleware' => ['auth:api', 'verified'], 'prefix' => 'v1'], function()
{
#users


#products
Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);
Route::get('/products/{product}/show', [ProductController::class, 'show']);
Route::post('/products/{product}/update', [ProductController::class, 'update']);
Route::delete('/products/{product}/delete', [ProductController::class, 'destroy']);



});
