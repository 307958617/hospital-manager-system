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
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/dep_user/org', 'DepartmentController@show_org')->name('show_org');
Route::get('/dep_user/org/get', 'DepartmentController@get_org')->name('get_org');

Route::get('/dep_user/departments', 'DepartmentController@show_departments')->name('show_departments');
Route::get('/dep_user/departments/get', 'DepartmentController@get')->name('departments.get');
Route::post('/dep_user/departments/change', 'DepartmentController@change')->name('departments.change');
Route::post('/dep_user/departments/add', 'DepartmentController@add')->name('departments.add');
Route::post('/dep_user/departments/edit', 'DepartmentController@edit')->name('departments.edit');
Route::post('/dep_user/departments/delete', 'DepartmentController@delete')->name('departments.delete');
Route::post('/dep_user/departments/deleteSelected', 'DepartmentController@deleteSelected')->name('departments.deleteSelected');

Route::get('/dep_user/users', 'UserController@show_users')->name('show_users');
Route::get('/dep_user/users/get', 'UserController@get')->name('users.get');
