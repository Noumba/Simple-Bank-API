<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/register', [App\Http\Controllers\Api\UserController::class, 'register']);
Route::post('/login', [App\Http\Controllers\Api\UserController::class, 'login']);

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::middleware('auth:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/create_bank_account', [App\Http\Controllers\BankAccountController::class, 'store']);
    Route::post('/update_bank_account/{id}', [App\Http\Controllers\BankAccountController::class, 'update']);
    Route::post('/transaction/{id?}', [App\Http\Controllers\TransactionController::class, 'performTransaction']);
    Route::get('/all_transactions', [App\Http\Controllers\TransactionController::class, 'transactions']);
});
