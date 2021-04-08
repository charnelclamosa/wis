<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('login', 'AuthenticationController@login');
Route::get('users/{id}', 'UserController@employeeInfo');
Route::get('count/bundles', 'DashboardController@bundles');
Route::get('count/bundles/scanned', 'DashboardController@scanned');
Route::get('count/bundles/encoded', 'DashboardController@encoded');
Route::get('count/bundles/out', 'DashboardController@out');
Route::get('overviews/bundles-out', 'DashboardController@outBundles');
Route::get('dashboard/logs', 'DashboardController@activityLogs');
Route::middleware(['auth:sanctum'])->group(function() {
    Route::post('logout', 'AuthenticationController@logout');
    Route::get('lumbers/lines', 'LumberLineController@index');
    Route::get('lumbers/locations', 'LumberLocationController@index');
    Route::get('roles', 'RoleController@index');

    Route::get('users', 'UserController@index');
    Route::post('users', 'UserController@store');
    Route::put('users/{id}', 'UserController@update');
    Route::patch('users/password/{id}', 'UserController@password');
    Route::patch('users/delete/{id}', 'UserController@delete');
    Route::patch('users/restore/{id}', 'UserController@restore');

    Route::post('print/bundles', 'PrintController@index');

    Route::get('lumbers', 'LumberController@index');
    Route::patch('lumbers/update/flag/{id}', 'LumberController@updateFlag');
    
    Route::post('bundles/{location}/{line}', 'BundleController@index');
    Route::get('bundles/print/{location}/{line}', 'BundleController@forPrinting');
    Route::get('bundles/id/latest', 'BundleController@idGenerator');
    Route::post('bundles/out/header/{id}', 'BundleController@shippedOutHeader');
    Route::post('bundles/out/details/{id}', 'BundleController@shippedOutDetail');
    Route::delete('bundles/in/header/{id}', 'BundleController@inHeader');
    Route::delete('bundles/in/details/{id}', 'BundleController@inDetails');
    
    Route::get('bundles/validate/received/{BundleNo}', 'BundleController@isExistReceived');
    Route::get('bundles/validate/scanned/{BundleNo}', 'BundleController@isExistScanned');
    Route::get('bundles/manual/id/latest', 'BundleController@idGeneratorLumber');
    Route::post('bundles/manual/header/{id}', 'BundleController@encodeHeader');
    Route::post('bundles/manual/details/{BundleNo}','BundleController@encodeDetails' );

    Route::get('logs/{id}', 'BundleLogController@index');
    Route::post('logs', 'BundleLogController@store');

    Route::get('divisions', 'DivisionController@index');
    Route::get('sections', 'SectionController@index');
    Route::get('purposes', 'PurposeController@index');
});


