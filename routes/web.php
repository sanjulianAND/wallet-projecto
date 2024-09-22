<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SoapController;

Route::post('/soap-server', [SoapController::class, 'handle']);
