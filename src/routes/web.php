<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\RequestController;

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

Route::middleware('auth')->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'attendanceView'])->name('attendance.view');
    Route::post('/attendance', [AttendanceController::class, 'updateStatus'])->name('update.status');
    Route::get('attendance/list', [AttendanceController::class, 'listView'])->name('attendance.list');
    Route::get('/attendance/{attendance_id}', [RequestController::class, 'detail'])->name('attendance.detail');
    Route::post('/attendance/{attendance_id}', [RequestController::class, 'storeRequest']);
    Route::get('/stamp_correction_request/list', [RequestController::class, 'listView'])->name('request.list');
});

