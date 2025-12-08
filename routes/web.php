<?php

use App\Http\Controllers\OcrController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/ocr', [OcrController::class, 'index'])->name('ocr.index');
Route::post('/ocr/upload', [OcrController::class, 'upload'])->name('ocr.upload');
Route::get('/ocr/select/{index}', [OcrController::class, 'select'])->name('ocr.select');
Route::post('/ocr/extractImmediately', [OcrController::class, 'extract']);
Route::post('/ocr/extract', [OcrController::class, 'extract'])->name('ocr.extract');
Route::delete('/ocr/{id}', [OcrController::class, 'destroy'])->name('ocr.destroy');
Route::post('/ocr/approve', [OcrController::class, 'approve'])->name('ocr.approve');

require __DIR__.'/auth.php';
