<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 1.系统相关
|--------------------------------------------------------------------------
|
| 系统相关：数据字典，数据字典详情，操作日志，自动化代码，文件管理
|
*/
Route::namespace('App\Http\Controllers\Admin\System')->group(function () {
    /** 自动化代码 */
    Route::group(['prefix' => 'autoCode', 'middleware' => ['auth.jwt']], function () {
        Route::get('/getDB', 'AutoCodeController@getDB');
        Route::get('/getTables', 'AutoCodeController@getTables');
        Route::get('/getColumn', 'AutoCodeController@getColumn');
        Route::post('/', 'AutoCodeController@autoCode');
    });
    /** 键值配置 */
    Route::group(['prefix' => 'config', 'middleware' => ['auth.jwt']], function () {
        Route::get('/', 'ConfigController@all');
        Route::get('/find/{id}', 'ConfigController@find');
        Route::get('/list', 'ConfigController@list');
        Route::post('/', 'ConfigController@create');
        Route::put('/{id}', 'ConfigController@update');
        Route::delete('/{id}', 'ConfigController@destroy');
    });
    /** 键值详情 */
    Route::group(['prefix' => 'configValue', 'middleware' => ['auth.jwt']], function () {
        Route::get('/', 'ConfigValueController@all');
        Route::get('/find/{id}', 'ConfigValueController@find');
        Route::get('/list', 'ConfigValueController@list');
        Route::post('/', 'ConfigValueController@create');
        Route::put('/{id}', 'ConfigValueController@update');
        Route::delete('/{id}', 'ConfigValueController@destroy');
    });
    /** 操作日志 */
    Route::group(['prefix' => 'accessLog', 'middleware' => ['auth.jwt']], function () {
        Route::get('/list', 'AccessLogController@list');
        Route::delete('/{id}', 'AccessLogController@destroy');
    });
});

/*
|--------------------------------------------------------------------------
| 1.权限相关
|--------------------------------------------------------------------------
|
| 系统相关：用户管理，菜单（路由）管理，角色权限管理
| 用户管理：注册/登录/修改密码/设置用户权限/删除用户/设置用户角色/分页获取用户列表
| 菜单管理：
| 角色管理：
|
*/
Route::namespace('App\Http\Controllers\Admin\Limits')->group(function () {
    /** 用户管理 */
    Route::group(['prefix' => 'user'], function () {
        Route::post('register', 'UserController@register');
        Route::post('login', 'UserController@login');
        Route::group(['middleware' => ['auth.jwt']], function () {
            Route::put('setAuthority/{uuid}', 'UserController@setAuthority');
            Route::put('/{id}', 'UserController@update');
            Route::post('list', 'UserController@userList');
            Route::post('loginOut', 'UserController@loginOut');
            Route::delete('/{id}', 'UserController@destroy');
            Route::post('changePassword', 'UserController@changePassword');
        });
    });
    /** 菜单管理 */
    Route::group(['prefix' => 'menu', 'middleware' => ['auth.jwt']], function () {
        Route::get('/', 'MenuController@all');
        Route::get('/async', 'MenuController@async');
        Route::get('/find/{id}', 'MenuController@find');
        Route::get('/list', 'MenuController@list');
        Route::post('/', 'MenuController@create');
        Route::put('/{id}', 'MenuController@update');
        Route::delete('/{id}', 'MenuController@destroy');
    });
    /** 角色管理 */
    Route::group(['prefix' => 'authority', 'middleware' => ['auth.jwt']], function () {
        Route::get('/', 'AuthorityController@all');
        Route::get('/find/{id}', 'AuthorityController@find');
        Route::get('/list', 'AuthorityController@list');
        Route::post('/', 'AuthorityController@create');
        Route::put('/{id}', 'AuthorityController@update');
        Route::delete('/{id}', 'AuthorityController@destroy');
    });
});

/** 2.基础数据 */
Route::namespace('App\Http\Controllers\Admin\Base')->group(function () {
    /** BaseArea管理 */
    Route::group(['prefix' => 'area', 'middleware' => ['auth.jwt']], function () {
        Route::get('/', 'AreaController@all');
        Route::get('/find/{id}', 'AreaController@find');
        Route::get('/list', 'AreaController@list');
        Route::post('/', 'AreaController@create');
        Route::put('/{id}', 'AreaController@update');
        Route::delete('/{id}', 'AreaController@destroy');
    });
    /** 文件管理 */
    Route::group(['prefix' => 'file', 'middleware' => ['auth.jwt']], function () {
        Route::post('/upload', 'FileController@upload');
        /*Route::get('/', 'FileController@all');
        Route::get('/find/{id}', 'FileController@find');
        Route::get('/list', 'FileController@list');
        Route::post('/', 'FileController@create');
        Route::put('/{id}', 'FileController@update');
        Route::delete('/{id}', 'FileController@destroy');*/
    });
});

/** 3.业务相关 */
Route::namespace('App\Http\Controllers\Admin\Business')->group(function () {
    /** DEMO文章管理 */
    Route::group(['prefix' => 'article', 'middleware' => ['auth.jwt']], function () {
        Route::get('/', 'ArticleController@all');
        Route::get('/find/{id}', 'ArticleController@find');
        Route::get('/list', 'ArticleController@list');
        Route::post('/', 'ArticleController@create');
        Route::put('/{id}', 'ArticleController@update');
        Route::delete('/{id}', 'ArticleController@destroy');
    });

    /** TestAuto管理 */
    Route::group(['prefix' => 'testAuto', 'middleware' => ['auth.jwt']], function () {
        Route::get('/', 'TestAutoController@all');
        Route::get('/find/{id}', 'TestAutoController@find');
        Route::get('/list', 'TestAutoController@list');
        Route::post('/', 'TestAutoController@create');
        Route::put('/{id}', 'TestAutoController@update');
        Route::delete('/{id}', 'TestAutoController@destroy');
    });

});
