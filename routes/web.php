<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OcrController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/ocr', [OcrController::class, 'index'])->name('ocr.index');
Route::post('/ocr/upload', [OcrController::class, 'upload'])->name('ocr.upload');
Route::post('/ocr/extract', [OcrController::class, 'extract'])->name('ocr.extract');
Route::post('/ocr', [OcrController::class, 'process'])->name('ocr.process');

