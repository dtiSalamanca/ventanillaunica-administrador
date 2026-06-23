<?php

use App\Http\Controllers\DependenciasController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


Route::get('/', function () {
    return view('auth.login');
});

Auth::routes(['register' => false]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('auth')->controller(DependenciasController::class)->group(function () {
    Route::get('/dependencias', 'indexDependencias')->name('indexDependencias');

});