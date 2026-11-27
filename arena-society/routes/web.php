<?php

use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\PainelController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [SessionController::class, 'create'])->name('login');
});

Route::post('/login', [SessionController::class, 'store'])->middleware('throttle:6,1')->name('login.store');

Route::middleware('auth')->group(function () {
    Route::get('/', [PainelController::class, 'index'])->name('painel');
    Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');
    Route::post('/reservas', [PainelController::class, 'cadastrarAgendamento'])->name('painel.agendamentos.store');
    Route::patch('/reservas/{agendamento}/status', [PainelController::class, 'atualizarStatusAgendamento'])->name('painel.agendamentos.status');
    Route::post('/times', [PainelController::class, 'cadastrarTime'])->name('painel.times.store');
    Route::put('/times/{time}', [PainelController::class, 'atualizarTime'])->name('painel.times.update');
    Route::delete('/times/{time}', [PainelController::class, 'excluirTime'])->name('painel.times.delete');
    Route::post('/catalogo', [PainelController::class, 'cadastrarProduto'])->name('painel.produtos.store');
    Route::put('/catalogo/{produto}', [PainelController::class, 'atualizarProduto'])->name('painel.produtos.update');
    Route::delete('/catalogo/{produto}', [PainelController::class, 'excluirProduto'])->name('painel.produtos.delete');
});
