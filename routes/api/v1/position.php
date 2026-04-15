<?php

use App\Http\Controllers\Api\PositionController;
use Illuminate\Support\Facades\Route;

Route::prefix('positions')->group(function () {

    Route::get('/', [PositionController::class, 'index'])
        ->middleware('permission:position.view');

    Route::get('/{position}', [PositionController::class, 'show'])
        ->middleware('permission:position.view');

    Route::post('/', [PositionController::class, 'store'])
        ->middleware('permission:position.create');

    Route::put('/{position}', [PositionController::class, 'update'])
        ->middleware('permission:position.update');

    Route::delete('/{position}', [PositionController::class, 'destroy'])
        ->middleware('permission:position.delete');

});