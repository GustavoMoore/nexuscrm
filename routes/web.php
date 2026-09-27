<?php

use App\Http\Controllers\DealController;
use App\Http\Controllers\FunnelController;
use App\Http\Controllers\LossReasonController;
use App\Http\Controllers\PasswordChangeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\StageController;
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
    Route::get('/projetos/{project}/funis/{funnel}', [FunnelController::class, 'show'])->scopeBindings()->name('funnels.show');
    Route::scopeBindings()->group(function () {
        Route::get('/projetos/{project}/funis/{funnel}/negocios', [DealController::class, 'index'])->name('deals.index');
        Route::post('/projetos/{project}/funis/{funnel}/negocios', [DealController::class, 'store']);
        Route::patch('/projetos/{project}/funis/{funnel}/negocios/{deal}', [DealController::class, 'update']);
        Route::post('/projetos/{project}/funis/{funnel}/negocios/{deal}/anotacoes', [DealController::class, 'note']);
        Route::post('/projetos/{project}/funis/{funnel}/negocios/{deal}/ganhar', [DealController::class, 'win']);
        Route::post('/projetos/{project}/funis/{funnel}/negocios/{deal}/perder', [DealController::class, 'lose']);
        Route::post('/projetos/{project}/funis/{funnel}/negocios/{deal}/reabrir', [DealController::class, 'reopen']);
    });
    Route::middleware('can:adm')->group(function () {
        Route::scopeBindings()->group(function () {
            Route::put('/projetos/{project}/funis/{funnel}/negocios/{deal}/responsaveis', [DealController::class, 'assignees']);
            Route::post('/projetos/{project}/motivos-perda', [LossReasonController::class, 'store']);
            Route::patch('/projetos/{project}/motivos-perda/{lossReason}', [LossReasonController::class, 'update']);
            Route::post('/projetos/{project}/motivos-perda/{lossReason}/desativar', [LossReasonController::class, 'deactivate']);
        });
        Route::post('/projetos', [ProjectController::class, 'store']);
        Route::patch('/projetos/{project}', [ProjectController::class, 'update']);
        Route::post('/projetos/{project}/arquivar', [ProjectController::class, 'archive']);
        Route::post('/projetos/{project}/desarquivar', [ProjectController::class, 'unarchive']);
        Route::scopeBindings()->group(function () {
            Route::post('/projetos/{project}/funis', [FunnelController::class, 'store']);
            Route::patch('/projetos/{project}/funis/{funnel}', [FunnelController::class, 'update']);
            Route::post('/projetos/{project}/funis/{funnel}/arquivar', [FunnelController::class, 'archive']);
            Route::post('/projetos/{project}/funis/{funnel}/desarquivar', [FunnelController::class, 'unarchive']);
            Route::post('/projetos/{project}/funis/{funnel}/etapas', [StageController::class, 'store']);
            Route::patch('/projetos/{project}/funis/{funnel}/etapas/{stage}', [StageController::class, 'update']);
            Route::post('/projetos/{project}/funis/{funnel}/etapas/{stage}/mover', [StageController::class, 'move']);
            Route::delete('/projetos/{project}/funis/{funnel}/etapas/{stage}', [StageController::class, 'destroy']);
        });
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
