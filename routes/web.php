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
Route::get('/department/org', 'DepartmentController@show_org')->name('show_org');
Route::get('/department/org/get', 'DepartmentController@get_org')->name('get_org');
Route::get('/department/departments', 'DepartmentController@show_departments')->name('show_departments');
Route::get('/department/users', 'DepartmentController@show_users')->name('show_users');
Route::get('/department/get', 'DepartmentController@get')->name('department.get');
Route::post('/department/change', 'DepartmentController@change')->name('department.change');
Route::post('/department/add', 'DepartmentController@add')->name('department.add');
Route::post('/department/edit', 'DepartmentController@edit')->name('department.edit');
Route::post('/department/delete', 'DepartmentController@delete')->name('department.delete');
Route::post('/department/deleteSelected', 'DepartmentController@deleteSelected')->name('department.deleteSelected');
