<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin_RemoteLogin;
use App\Http\Controllers\Admin_RemoteUser;
use App\Http\Controllers\Admin_Authors;
use App\Http\Controllers\Admin_Books;


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

Route::get('/', [Admin_RemoteLogin::class, 'check_login']);

Route::get('/user', [Admin_RemoteLogin::class, 'check_login']);

Route::get('/login', [Admin_RemoteLogin::class, 'show_login_form'])->name('login');

Route::post('/remote_log_in', [Admin_RemoteLogin::class, 'remote_log_in']);

Route::get('/user', [Admin_RemoteUser::class, 'show_user']);

Route::get('/logout', [Admin_RemoteLogin::class, 'log_out']);

Route::get('/authors', [Admin_Authors::class, 'list_authors']);

Route::get('/authors/{page_no}', [Admin_Authors::class, 'list_authors']);

Route::get('/author/{author_id}', [Admin_Authors::class, 'single_author']);

Route::post('/delete-author', [Admin_Authors::class, 'delete_author']);

Route::post('/delete-book', [Admin_Books::class, 'delete_book']);

Route::get('/add-book', [Admin_Books::class, 'show_add_book_form']);

Route::post('/add-book', [Admin_Books::class, 'add_book']);



