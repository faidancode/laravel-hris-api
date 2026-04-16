<?php

use App\Http\Controllers\Api\RoleController;
use Illuminate\Support\Facades\Route;

Route::prefix('roles')->group(function () {

    Route::get('/', [RoleController::class, 'index'])
        ->middleware('permission:role.view');

    Route::post('/', [RoleController::class, 'store'])
        ->middleware('permission:role.create');

    Route::put('/{id}/permissions', [RoleController::class, 'syncPermissions'])
        ->middleware('permission:role.assign-permission');

});