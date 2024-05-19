<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'rate',
    'controller' => \App\Http\Controllers\RateController::class
], function () {
    Route::get('info', 'info');
    Route::get('history', 'history');
});
