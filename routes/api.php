<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CongregationsController;
use App\Http\Controllers\Api\PublishersController;
use App\Http\Controllers\Api\StandPublishersController;
use App\Http\Controllers\Api\StandTemplateController;
use App\Http\Controllers\Api\BuilderAssistant\WarehouseController;
use Illuminate\Support\Facades\Route;

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

Route::get('/index', static function () {
    return 'Wow!';
});

Route::prefix('auth')->group(static function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api');
    Route::get('/user-profile', [AuthController::class, 'userProfile'])->middleware('auth:api');
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::apiResource('publishers', PublishersController::class);
    Route::apiResource('congregations', CongregationsController::class);

    Route::post('stand/publishers', [StandPublishersController::class, 'store']);
    Route::put('stand/publishers', [StandPublishersController::class, 'update']);
    Route::delete('stand/publishers', [StandPublishersController::class, 'destroy']);

    Route::get('stand/templates', [StandTemplateController::class, 'index']);
    Route::get('week_days', [StandTemplateController::class, 'weekDays']);

    Route::get('warehouse', [WarehouseController::class, 'index']);
    Route::post('warehouse', [WarehouseController::class, 'store']);
});
