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

Route::get('/', function () {
//    return view('welcome');
    return redirect('/threads');
});

Auth::routes();

Route::get('/home',             'HomeController@index')
    ->name('home');

// ThreadsController...
Route::get ('/threads', 'ThreadsController@index');
Route::post('/threads', 'ThreadsController@store');
Route::get ('/threads/create', 'ThreadsController@create');
Route::get ('/threads/{channel}', 'ThreadsController@index');
Route::get ('/threads/{channel}/{thread}', 'ThreadsController@show');
Route::delete ('/threads/{channel}/{thread}', 'ThreadsController@destroy');

// RepliesController...
Route::post('/threads/{channel}/{thread}/replies', 'RepliesController@store');
Route::delete('/replies/{reply}', 'RepliesController@destroy');

// FavoritesController..
Route::post('/replies/{reply}/favorites', 'FavoritesController@show');

// ProfilesController...
Route::get('/profiles/{user}', 'ProfilesController@show')->name('profile');
