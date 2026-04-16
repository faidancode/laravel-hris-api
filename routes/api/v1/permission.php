<?php

use App\Http\Controllers\Api\PermissionController;
use Illuminate\Support\Facades\Route;

Route::prefix('permissions')->group(function () {

    Route::get('/', [PermissionController::class, 'index'])
        ->middleware('permission:permission.view');

});