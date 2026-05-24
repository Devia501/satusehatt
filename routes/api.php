<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EncounterController;

Route::post('/encounter', [EncounterController::class, 'store']);