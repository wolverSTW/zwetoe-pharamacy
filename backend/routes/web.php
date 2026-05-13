<?php

use Illuminate\Support\Facades\Route;

Route::get('/invoice/{sale}', function (App\Models\Sale $sale) {
    return view('invoice', compact('sale'));
})->name('invoice.print');
use App\Livewire\CustomLogin;

Route::get('/login', CustomLogin::class)->name('login');
Route::redirect('/', '/login');
Route::post('/logout', function() {
    auth()->logout();
    return redirect('/login');
})->name('logout');