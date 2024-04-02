<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/', [PostApiController::class, 'postForm'])->name('form.api');
Route::get('/{post_id}/edit', [PostApiController::class, 'edit'])->name('edit.api');

Route::get('/showAll', [PostApiController::class, 'showAll'])->name('showAll.api');
Route::get('/show/{post_id}', [PostApiController::class, 'show'])->name('show.api');

Route::post('/upload', [PostApiController::class, 'uploadCreate'])->name('ckeditor.upload.api');
Route::post('/create', [PostApiController::class, 'create'])->name('create.api');
Route::put('/{post_id}/update', [PostApiController::class, 'update'])->name('update.api');
Route::delete('/{post_id}/delete', [PostApiController::class, 'delete'])->name('delete.api');

// testing purposes
Route::get('/getFiles/{post_id}', [PostApiController::class, 'getFileFromFolder']);
