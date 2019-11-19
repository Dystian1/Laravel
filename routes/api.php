<?php

use Illuminate\Http\Request;

Route::post('register', 'UserController@register');
Route::post('login', 'UserController@login');

Route::middleware(['jwt.verify'])->group(function(){

	Route::get('/book/{limit}/{offset}', 'BookController@getAll');
	Route::post('/book/register', 'BookController@register');
	Route::post('/book/ubah', "BookController@ubah");
	Route::get('/book/{id}', 'BookController@show');
	Route::post('/book', 'BookController@store');
	Route::put('/book/{id}', 'BookController@update');
	Route::delete('/book/{id}', 'BookController@destroy');

	Route::get('count', "DashboardController@dashboard");

	//user
	Route::get('user/{limit}/{offset}', "UserController@getAll");
	Route::post('user/{limit}/{offset}', "UserController@find");
	Route::delete('user/delete/{id}', "UserController@delete");
	Route::post('user/ubah', "UserController@ubah");

	//cek login
	Route::get('user/check' , "UserController@getAuthenticatedUser");

	//Peminjam
	Route::post('pinjam/{id}', "PinjamController@index");
	Route::delete('kembali/{id}', "PinjamController@kembali");
	Route::get('pinjam/{limit}/{offset}', "PinjamController@getAll");
});