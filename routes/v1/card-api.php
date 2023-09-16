<?php

use App\Http\Controllers\Api\V1\CardController;
use Illuminate\Support\Facades\Route;

Route::post('card-to-card', [CardController::class, 'cardToCard'])->name('card-to-card');
