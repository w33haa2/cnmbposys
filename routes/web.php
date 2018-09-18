<?php

Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');
Auth::routes();

/*
|------------------------------------------------------------------------------------
| Admin
|------------------------------------------------------------------------------------
*/
Route::group([ 'middleware'=>['auth']], function () {
    Route::get('/', 'DashboardController@index')->name('dashboard');
    Route::resource('users', 'UserController');
    Route::get('/email', function () {
        return view('admin.dashboard.email');
    });
    Route::get('/forms', function () {
        return view('admin.dashboard.forms');
    });
});
