<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [PostController::class, 'postForm'])->name('form');
Route::get('/{post_id}/edit', [PostController::class, 'edit'])->name('edit');

Route::get('/showAll', [PostController::class, 'showAll'])->name('showAll');
Route::get('/show/{post_id}', [PostController::class, 'show'])->name('show');

Route::post('/upload_create', [PostController::class, 'uploadCreate'])->name('ckeditor.upload.create');
Route::post('/upload_update', [PostController::class, 'uploadUpdate'])->name('ckeditor.upload.update');
Route::post('/create', [PostController::class, 'create'])->name('create');
Route::put('/{post_id}/update', [PostController::class, 'update'])->name('update');
Route::delete('/{post_id}/delete', [PostController::class, 'delete'])->name('delete');
