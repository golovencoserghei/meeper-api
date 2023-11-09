<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegistrationController;
use App\Http\Controllers\Api\BuilderAssistant\WarehouseController;
use App\Http\Controllers\Api\CongregationsController;
use App\Http\Controllers\Api\PermissionsController;
use App\Http\Controllers\Api\PublishersController;
use App\Http\Controllers\Api\RolesController;
use App\Http\Controllers\Api\StandController;
use App\Http\Controllers\Api\StandRecordsController;
use App\Http\Controllers\Api\StandReportsController;
use App\Http\Controllers\Api\StandTemplateController;
use App\Http\Controllers\Api\UserActionsController;
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
    Route::post('/login', [LoginController::class, 'login'])->name('login');
    Route::post('/register', [RegistrationController::class, 'register']);
    Route::post('/self-register', [RegistrationController::class, 'selfRegister']);
});

Route::group(['middleware' => 'auth:api'], static function () {
    Route::get('auth/user-profile', [LoginController::class, 'userProfile']);
    Route::post('auth/logout', [LoginController::class, 'logout']);
    Route::post('auth/refresh', [LoginController::class, 'refresh']);
    Route::get('user/permission', [LoginController::class, 'userPermissions']);

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

    Route::apiResource('publishers', PublishersController::class)->except(['show']);
    Route::get('publishers/my-records', [PublishersController::class, 'myRecords']);

    Route::apiResource('congregations', CongregationsController::class);
    Route::post('congregations/add-user', [CongregationsController::class, 'addUserToCongregation']);

    Route::post('stand/records', [StandRecordsController::class, 'store']);
    Route::post('stand/records/{id}', [StandRecordsController::class, 'removePublishers']);
    Route::get('stand/records/{id}', [StandRecordsController::class, 'show']);
    Route::put('stand/records/{id}', [StandRecordsController::class, 'update']);
    Route::delete('stand/records/{id}', [StandRecordsController::class, 'destroy']);

    Route::apiResource('stands', StandController::class);
//        ->middleware('role:' . RolesEnum::RESPONSIBLE_FOR_STAND->value . '|' . RolesEnum::ADMIN->value);

    Route::apiResource('stand/templates', StandTemplateController::class);
    Route::get('stand/weekly-ranges', [StandTemplateController::class, 'weeklyRanges']);

    Route::apiResource('stand/reports', StandReportsController::class);

    Route::get('/logger', [UserActionsController::class, 'index']);

    Route::get('warehouse', [WarehouseController::class, 'index']);
    Route::post('warehouse', [WarehouseController::class, 'store']);
});
