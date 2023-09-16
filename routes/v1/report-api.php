<?php

use App\Http\Controllers\Api\V1\ReportController;
use Illuminate\Support\Facades\Route;

Route::prefix('cards')->name('cards.')->group(function (){
    Route::get('top-users', [ReportController::class, 'topCardToCardUsers'])->name('top-users');
});




