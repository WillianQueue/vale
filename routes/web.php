<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Página inicial (sem login) - HOME com CHAT
Route::get('/', function () {
    return view('home');
})->name('home');

// Rotas públicas (sem login)
Route::middleware('guest')->group(function () {
    // Autenticação
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    
    // Demo login (testar sem criar conta)
    Route::post('/demo-login', [AuthController::class, 'demoLogin'])->name('demo.login');
});

// Rotas protegidas (requer login)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Chat
    Route::get('/chat', function () {
        return view('chat');
    })->name('chat');
    
    // Dashboard (redireciona para chat)
    Route::get('/dashboard', function () {
        return redirect('/chat');
    })->name('dashboard');
});
