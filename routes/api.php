<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CongregationsController;
use App\Http\Controllers\Api\PermissionsController;
use App\Http\Controllers\Api\PublishersController;
use App\Http\Controllers\Api\RolesController;
use App\Http\Controllers\Api\StandController;
use App\Http\Controllers\Api\StandRecordsController;
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

Route::group(['middleware' => 'auth:api'], static function () {
    Route::group(
        [
            'middleware' => ['role:admin'],
            'prefix' => 'admin',
        ],
        static function () {
            Route::apiResource('permissions', PermissionsController::class);
            Route::apiResource('roles', RolesController::class);

            Route::post('roles/assign-permissions-to-role', [RolesController::class, 'assignPermissionToRole']);
            Route::get('roles/{id}/permissions', [RolesController::class, 'getRolePermissions']);
            Route::get('roles/{id}/users', [RolesController::class, 'getRoleUsers']);

            Route::post('roles/assign-role-to-user', [RolesController::class, 'assignRoleToUser']);
            Route::post('roles/unassign-user-role', [RolesController::class, 'unassignUserRole']);
        }
    );

    Route::apiResource('publishers', PublishersController::class);

    Route::apiResource('congregations', CongregationsController::class);

    Route::post('stand/records', [StandRecordsController::class, 'store']);
    Route::post('stand/records/{id}', [StandRecordsController::class, 'removePublishers']);
    Route::get('stand/records/{id}', [StandRecordsController::class, 'show']);
    Route::put('stand/records/{id}', [StandRecordsController::class, 'update']);
    Route::delete('stand/publishers', [StandRecordsController::class, 'destroy']);

    Route::get('stands', [StandController::class, 'index']);

    Route::apiResource('stand/templates', StandTemplateController::class);

    Route::get('warehouse', [WarehouseController::class, 'index']);
    Route::post('warehouse', [WarehouseController::class, 'store']);
});
