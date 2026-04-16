<?php

use App\Http\Controllers\Api\DepartmentController;
use Illuminate\Support\Facades\Route;

Route::prefix('departments')->group(function () {

    Route::get('/', [DepartmentController::class, 'index'])
        ->middleware('permission:department.view');

    Route::get('/{department}', [DepartmentController::class, 'show'])
        ->middleware('permission:department.view');

    Route::post('/', [DepartmentController::class, 'store'])
        ->middleware('permission:department.create');

    Route::put('/{department}', [DepartmentController::class, 'update'])
        ->middleware('permission:department.update');

    Route::delete('/{department}', [DepartmentController::class, 'destroy'])
        ->middleware('permission:department.delete');

});