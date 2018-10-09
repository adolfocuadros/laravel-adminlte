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

/*
 * Autenticación
 */

Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');

Route::get('password/reset', function() {
    return view('admin.pages.public.password_recover');
})->name('password.request');


Route::post('login', 'Auth\LoginController@login');
Route::get('login', function() {
    return view('admin.pages.public.login');
})->name('login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

Route::middleware('auth')->namespace('Admin')->group(function() {
    Route::get('panel/acerca-de', function() {
        return view('admin.pages.acerca_de');
    })->name('panel.acerca_de');
    Route::get('/', 'DashboardController@index');
    Route::get('panel/', 'DashboardController@index')->name('dashboard');

    \App\Lib\Helper::RoutesCRUD('panel/usuarios', 'UserController', 'panel.usuarios', '{user}');
});
