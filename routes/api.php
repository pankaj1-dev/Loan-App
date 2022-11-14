<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PassportAuthController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\LoanRepaymentController;
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

Route::post('register', [PassportAuthController::class, 'register']);
Route::post('login', [PassportAuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('loans', [LoanController::class, 'loans']);
    Route::get('loans/{id}', [LoanController::class, 'singleLoan']);
    Route::post('loans', [LoanController::class, 'createLoan']);
    Route::put('loans/{id}', [LoanController::class, 'updateLoan']);
    Route::put('repayment/{id}', [LoanRepaymentController::class, 'loanRepayment']);
});
