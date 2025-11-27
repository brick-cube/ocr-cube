<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OcrController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/ocr', [OcrController::class, 'index']);
Route::post('/ocr', [OcrController::class, 'process'])->name('ocr.process');

