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
Route::prefix('biometric')->group(function() {
  Route::get('users', 'TAD@users');
  Route::get('attendance-logs', 'TAD@attendanceLogs');
  Route::get('reports', 'TAD@reports');
  Route::get('reports/late-undertime-group', 'TAD@reportsLateUndertimeGroup');
  Route::get('reports/late-undertime-individual', 'TAD@reportsLateUndertimeIndividual');
});
