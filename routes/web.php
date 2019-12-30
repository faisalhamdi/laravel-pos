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
Route::get('/', function() {
    return redirect(route('login'));
});
Auth::routes();
Route::group(['middleware' => 'auth'], function() {

    //Role admin
    Route::group(['middleware' => ['role:admin']], function() {
        Route::resource('/role', 'RoleController')->except([
            'create', 'show', 'edit', 'update'
        ]);

        Route::resource('/users', 'UserController')->except([
            'show'
        ]);
        Route::get('/users/roles/{id}', 'UserController@roles')->name('users.roles');
        Route::put('/users/roles/{id}', 'UserController@setRole')->name('users.set_role');
        Route::post('/users/permission', 'UserController@addPermission')->name('users.add_permission');
        Route::get('/users/role-permission', 'UserController@rolePermission')->name('users.roles_permission');
        Route::put('/users/permission/{role}', 'UserController@setRolePermission')->name('users.setRolePermission');
    });

    // Role this permission
    Route::group(['middleware' => ['permission:show products|create products|delete products']], function() {
        Route::resource('/categories', 'CategoryController')->except([
            'create', 'show'
        ]);
        Route::resource('/products', 'ProductController');
    });

    // Role casier
    Route::group(['middleware' => ['role:cashier']], function() {
        Route::get('/transaction', 'OrderController@addOrder')->name('order.transaction');
        Route::get('/checkhout', 'OrderController@addOrder')->name('order.checkout');
    });

    Route::get('/home', 'HomeController@index')->name('home');
});

Route::get('/distance', 'TestController@getDistance');