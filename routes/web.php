<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ChatController::class, 'index'])
    ->name('home');

Route::get('/chat', function () {
    return redirect()->route('home');
})->name('chat');

Route::post('/chat/enviar', [ChatController::class, 'enviar'])
    ->name('chat.enviar');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.post');

    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->name('register.post');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    Route::get('/dashboard', function () {
        return redirect()->route('home');
    })->name('dashboard');

    Route::delete('/chat/{conversation}', [ChatController::class, 'destroy'])
        ->name('chat.destroy');
});