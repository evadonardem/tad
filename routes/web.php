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
Route::get('/', 'TAD@dashboard');

Route::get('login', 'TAD@login');

Route::prefix('biometric')->group(function () {
    Route::get('users', 'TAD@users');
    Route::get('attendance-logs', 'TAD@attendanceLogs');
    Route::get('override-logs', 'TAD@overrideLogs');
    Route::get('reports', 'TAD@reports');
    Route::get('reports/late-undertime-group', 'TAD@reportsLateUndertimeGroup');
    Route::get('reports/late-undertime-individual', 'TAD@reportsLateUndertimeIndividual');
    Route::get('reports/absences-group', 'TAD@reportsAbsencesGroup');
    Route::get('reports/absences-individual', 'TAD@reportsAbsencesIndividual');
});


Route::prefix('settings')->group(function () {
    Route::get('/', 'TAD@settings');
    Route::get('/common-time-shifts', 'TAD@settingsCommonTimeShifts');
    Route::get('/user-roles', 'TAD@settingsUserRoles');
});
