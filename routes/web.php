<?php

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect('/login');
});

Route::middleware('guest')->group(function (){
    Route::get('/login', 'Auth\LoginController@loginForm')->name('login');
    Route::post('/login', 'Auth\LoginController@login');
});


Route::get('/home', function () {
    return redirect('/admin/dashboard');
});

Route::middleware('auth')->group(function (){
    Route::get('/admin/{any}', 'IndexController@singleApp')->where('any', '.*');

    Route::prefix('api')->group(function () {
        Route::resource('permissions', 'RBAC\PermissionController');
        Route::post('general', 'RBAC\ConfigurationController@getGeneralData');
        Route::get('configurations', 'RBAC\ConfigurationController@getConfigurations');
        Route::resource('user', 'RBAC\UserController');
        Route::resource('role', 'RBAC\RoleController');
        Route::resource('module', 'RBAC\ModuleController');
        Route::resource('role_module', 'RBAC\RoleModuleController');
        Route::resource('settings', 'ConfigurationController');
    });
});
