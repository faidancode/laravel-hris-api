<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')->group(function () {

    Route::get('/', [UserController::class, 'index'])
        ->middleware('permission:user.view');

    Route::post('/', [UserController::class, 'store'])
        ->middleware('permission:user.create');

    Route::get('/{id}', [UserController::class, 'show'])
        ->middleware('permission:user.view');

    Route::put('/{id}', [UserController::class, 'update'])
        ->middleware('permission:user.update');

    Route::delete('/{id}', [UserController::class, 'destroy'])
        ->middleware('permission:user.delete');

    Route::post('/{id}/assign-role', [UserController::class, 'assignRole'])
        ->middleware('permission:user.assign-role');
});