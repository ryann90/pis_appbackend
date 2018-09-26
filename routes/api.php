<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middelware' => ['cors', 'web']], function (){

    Route::post('register', 'TenantController@registerTenant');
    Route::post('login', 'TenantLoginController@login');
    Route::get('subscriber/{company}', 'TenantController@subscriberChecker');

    // Needed to have api token (soon!)

    // employee controller
    Route::resource('employees','TenantEmployeeController');
    Route::get('employees/{id}/archive','TenantEmployeeController@delete')->name('employees.archive');
    Route::post('employees/upload','TenantEmployeeController@uploadImage')->name('employees.upload');

    // movement controller
    Route::resource('movement','EmployeeMovementController');
    // department controller
    Route::resource('department','EmployeeDepartmentController');
});
