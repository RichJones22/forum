<?php declare(strict_types=1);
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

Route::get('/home', 'HomeController@index')
    ->name('home');

// ThreadsController...
Route::get('/threads', 'ThreadsController@index');
Route::post('/threads', 'ThreadsController@store');
Route::get('/threads/create', 'ThreadsController@create');
Route::get('/threads/{channel}', 'ThreadsController@index');
Route::get('/threads/{channel}/{thread}', 'ThreadsController@show');
Route::delete('/threads/{channel}/{thread}', 'ThreadsController@destroy');
Route::post('/threads/{channel}/{thread}/subscriptions', 'ThreadSubscriptionsController@store')
    ->middleware('auth');
Route::delete('/threads/{channel}/{thread}/subscriptions', 'ThreadSubscriptionsController@destroy')
    ->middleware('auth');

// RepliesController...
Route::get('/threads/{channel}/{thread}/replies', 'RepliesController@index');
Route::post('/threads/{channel}/{thread}/replies', 'RepliesController@store');
Route::patch('/replies/{reply}', 'RepliesController@update');
Route::delete('/replies/{reply}', 'RepliesController@destroy');

// FavoritesController..
Route::post('/replies/{reply}/favorites', 'FavoritesController@show');
Route::delete('/replies/{reply}/favorites', 'FavoritesController@destroy');

// ProfilesController...
Route::get('/profiles/{user}', 'ProfilesController@show')->name('profile');
Route::get('/profiles/{user}/notifications', 'UserNotificationsController@index');
Route::delete('/profiles/{user}/notifications/{notification}', 'UserNotificationsController@destroy');
