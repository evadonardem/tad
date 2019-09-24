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

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
    $api->get('/', function () {
        return 'TAD API';
    });

    $api->post('login', 'App\Http\Controllers\Api\V1\AuthController@login');

    $api->group(['middleware' => 'api.auth'], function ($api) {
        $api->post('logout', 'App\Http\Controllers\Api\V1\AuthController@logout');
        $api->post('refresh', 'App\Http\Controllers\Api\V1\AuthController@refresh');
        $api->post('me', 'App\Http\Controllers\Api\V1\AuthController@me');
    });
    
    $api->group(['prefix' => 'biometric'], function ($api) {
        $api->get('info', 'App\Http\Controllers\Api\V1\BiometricInfoController@index');
        $api->resource('users', 'App\Http\Controllers\Api\V1\BiometricUsersController');
        $api->get('attendance-logs', 'App\Http\Controllers\Api\V1\BiometricAttendanceController@index');
    });

    $api->group(['prefix' => 'reports'], function ($api) {
        $api->get('late-undertime', 'App\Http\Controllers\Api\V1\ReportsController@lateUndertime');
    });

    $api->group(['prefix' => 'settings'], function ($api) {
        $api->resource('common-time-shifts', 'App\Http\Controllers\Api\V1\CommonTimeShiftsController');
    });


    // Utilities
    $api->get('sync-admin-users', 'App\Http\Controllers\Api\V1\BiometricUsersController@syncAdminUsers');
    
});
