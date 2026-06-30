<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\CreatorController;
use App\Http\Controllers\ExcelController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Core Application Routes (Requires Auth)
Route::middleware(['auth'])->group(function () {
    Route::get('/', [QuizController::class, 'index'])->name('dashboard');
    Route::get('/leaderboard', [QuizController::class, 'leaderboard'])->name('leaderboard');
    Route::get('/profile', [QuizController::class, 'profile'])->name('profile');
    
    // Playing Quizzes
    Route::get('/quiz/{quiz}', [QuizController::class, 'play'])->name('quiz.play');
    Route::post('/quiz/{quiz}/submit', [QuizController::class, 'submitAttempt'])->name('quiz.submit');
    Route::get('/quiz/attempt/{attempt}', [QuizController::class, 'showAttempt'])->name('quiz.attempt');

    // Creator Studio
    Route::get('/creator', [CreatorController::class, 'index'])->name('creator.index');
    Route::get('/creator/create', [CreatorController::class, 'create'])->name('creator.create');
    Route::post('/creator/store', [CreatorController::class, 'store'])->name('creator.store');
    Route::get('/creator/{quiz}/edit', [CreatorController::class, 'edit'])->name('creator.edit');
    Route::post('/creator/{quiz}/update', [CreatorController::class, 'update'])->name('creator.update');
    Route::post('/creator/{quiz}/delete', [CreatorController::class, 'destroy'])->name('creator.delete');
    
    Route::post('/creator/{quiz}/question', [CreatorController::class, 'storeQuestion'])->name('creator.question.store');
    Route::post('/creator/question/{question}/delete', [CreatorController::class, 'deleteQuestion'])->name('creator.question.delete');

    // Excel Operations
    Route::get('/creator/template/download', [ExcelController::class, 'downloadTemplate'])->name('creator.template.download');
    Route::post('/creator/{quiz}/import', [ExcelController::class, 'import'])->name('creator.import');
    Route::get('/creator/{quiz}/export', [ExcelController::class, 'exportResults'])->name('creator.export');
});
