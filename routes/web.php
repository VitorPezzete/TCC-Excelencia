<?php

/*
Métodos HTTP:(
Get -> Usuário pediu para visualizar a página
Post -> Usuário está enviando dados)

Middleware: É um filtro que roda antes de chegar a rota/página.
Guest: Só deixa passar quem não está logado.
Auth: Só deixa passar quem está logado.
*/

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CardapioController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/cardapio', [CardapioController::class, 'index'])->name('cardapio');

Route::get('/login', function () {
    return view('login');
})->name('login')->middleware('guest');

Route::post('/cadastro', [AuthController::class, 'register'])->name('register')->middleware('guest');
Route::post('/login',    [AuthController::class, 'login'])->name('login.post')->middleware('guest');
Route::post('/logout',   [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/perfil', [ProfileController::class, 'index'])->name('perfil');
    Route::post('/perfil/dados', [ProfileController::class, 'updateData'])->name('perfil.dados');
    Route::post('/perfil/senha', [ProfileController::class, 'updatePassword'])->name('perfil.senha');
    Route::post('/perfil/enderecos', [ProfileController::class, 'storeAddress'])->name('perfil.enderecos.store');
    Route::delete('/perfil/enderecos/{id}', [ProfileController::class, 'destroyAddress'])->name('perfil.enderecos.destroy');
    Route::post('/perfil/enderecos/{id}/padrao', [ProfileController::class, 'setDefaultAddress'])->name('perfil.enderecos.padrao');
    Route::put('/perfil/enderecos/{id}', [ProfileController::class, 'updateAddress'])->name('perfil.enderecos.update');
});