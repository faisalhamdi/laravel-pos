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
    return redirect(route('login'));
});
Auth::routes();

Route::group(['middleware' => 'auth'], function() {
    Route::resource('categories', 'CategoryController')->except([
        'create', 'show'
    ]);
    Route::resource('products', 'ProductController');
    Route::get('/home', 'HomeController@index')->name('home');

});

Route::get('/distance', 'TestController@getDistance');

// Route::resource('categories', 'CategoryController')->except(['create', 'show']);

// Route::resource('products', 'ProductController');

// Auth::routes();

// Route::get('/home', 'HomeController@index')->name('home');

// Auth::routes();

// Route::get('/home', 'HomeController@index')->name('home');
