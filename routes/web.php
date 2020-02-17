<?php

use App\Http\Controllers\LegalController;
use App\Http\Controllers\DebugController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['verify' => true]);
Route::get('login/{provider}', 'Auth\LoginController@redirectToProvider');
Route::get('login/{provider}/callback', 'Auth\LoginController@handleProviderCallback');

Route::get('/home', 'HomeController@index')->name('home');

// something is wrong here
Route::get('/landing', function () {
    return view('landing');
});

Route::get('/privacy', [LegalController::class, 'privacy']);
Route::get('/tos', [LegalController::class, 'tos']);

//Route::group();
Route::post('/admin/season/create');

//=== DEBUG ===//
Route::get('/xyz', [DebugController::class, 'xyz']);

//===SAMPLE ROUTE===//
//Route::get('/route', function () {
//    return view('dir.file');
//});
