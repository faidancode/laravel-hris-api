<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    require __DIR__ . '/api/v1/auth.php';
});

Route::prefix('v1')->middleware(['auth:api'])->group(function () {
    require __DIR__ . '/api/v1/department.php';
    require __DIR__ . '/api/v1/position.php';
    require __DIR__ . '/api/v1/user.php';
});