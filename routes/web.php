<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\FileController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Autenticação (Apenas Convidados)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Rotas Protegidas (Apenas Usuários Logados)
Route::middleware('auth')->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat');
    Route::post('/chat/enviar', [ChatController::class, 'enviar'])->name('chat.enviar');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Rotas do Módulo do Administrador (Apenas Admin)
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/files', [FileController::class, 'index'])->name('files.index');
        Route::post('/files/upload', [FileController::class, 'upload'])->name('files.upload');
        Route::delete('/files/{nome}', [FileController::class, 'deletar'])->name('files.deletar');
    });
});
