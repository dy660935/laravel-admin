<?php
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

Route::any('/', '\App\Admin\Controllers\LoginController@index');
include_once("admin.php");
//Auth::routes();
//Route::get('/', 'UserController@login')->name('login');
Route::get('/', '\App\Admin\Controllers\LoginController@index')->name('login');
//Route::get('/home', 'HomeController@index')->name('home');