<?php

use App\Http\Controllers\PasswordChangeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => redirect(auth()->check() ? '/agenda' : '/login'))->name('home');
Route::middleware('auth')->group(function () {
    Route::get('/trocar-senha', [PasswordChangeController::class, 'edit'])->name('password.change');
    Route::put('/trocar-senha', [PasswordChangeController::class, 'update']);
    Route::get('/agenda', fn () => Inertia::render('agenda'))->name('agenda');
    Route::get('/projetos', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projetos/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::middleware('can:adm')->group(function () {
        Route::post('/projetos', [ProjectController::class, 'store']);
        Route::patch('/projetos/{project}', [ProjectController::class, 'update']);
        Route::post('/projetos/{project}/arquivar', [ProjectController::class, 'archive']);
        Route::post('/projetos/{project}/desarquivar', [ProjectController::class, 'unarchive']);
        Route::get('/usuarios', [UserController::class, 'index'])->name('users.index');
        Route::post('/usuarios', [UserController::class, 'store']);
        Route::patch('/usuarios/{user}', [UserController::class, 'update']);
        Route::post('/usuarios/{user}/desativar', [UserController::class, 'deactivate']);
        Route::post('/usuarios/{user}/reativar', [UserController::class, 'activate']);
        Route::post('/usuarios/{user}/redefinir-senha', [UserController::class, 'resetPassword']);
    });
});
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
