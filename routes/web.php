<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

// Página inicial pública
Route::get('/', function () {
    return view('home');
})->name('home');

// Rotas públicas de autenticação
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.post');

    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->name('register.post');

    Route::post('/demo-login', [AuthController::class, 'demoLogin'])
        ->name('demo.login');
});

// Rotas protegidas por login
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    // Exibe o chat e carrega o histórico do usuário
    Route::get('/chat', [ChatController::class, 'index'])
        ->name('chat');

    // Envia a pergunta para o Ollama
    Route::post('/chat/enviar', [ChatController::class, 'enviar'])
        ->name('chat.enviar');

    // Dashboard redireciona para o chat
    Route::get('/dashboard', function () {
        return redirect()->route('chat');
    })->name('dashboard');
});