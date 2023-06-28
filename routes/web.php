<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// books
Route::resource('/books', App\Http\Controllers\BookController::class);
Route::post('/books/table', [App\Http\Controllers\BookController::class, 'table'])->name('books.table');
Route::get('/books/detail/{id}', [App\Http\Controllers\BookController::class, 'detail'])->name('books.detail');
Route::post('/books/import', [App\Http\Controllers\BookController::class, 'import'])->name('books.import');

Route::get('/', [App\Http\Controllers\BookController::class, 'index']);

// Route::get('/', function () {
//     return view('welcome');
// });
