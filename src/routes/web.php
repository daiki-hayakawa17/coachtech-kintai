<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\AdminListController;

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

Route::middleware('auth', 'verified')->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'attendanceView'])->name('attendance.view');
    Route::post('/attendance', [AttendanceController::class, 'updateStatus'])->name('update.status');
    Route::get('attendance/list', [AttendanceController::class, 'listView'])->name('attendance.list');
    Route::get('/attendance/{attendance_id}', [RequestController::class, 'detail'])->name('attendance.detail');
    Route::post('/attendance/{attendance_id}', [RequestController::class, 'storeRequest']);
    Route::get('/stamp_correction_request/list', [RequestController::class, 'listView'])->name('request.list');
});

Route::get('/admin/login', function () {
    return view('admin.login');
})->name('admin.login');

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::middleware('admin')->group(function () {
    Route::get('/admin/list', [AdminListController::class, 'listView'])->name('admin.list');
    Route::get('/admin/staff/list', [AdminListController::class, 'staffListView'])->name('admin.staff');
    Route::get('admin/attendance/staff/{user_id}', [AdminListController::class, 'attendanceList'])->name('staff.attendance');
    Route::get('stamp_correction_request/approved/{attendance_correct_request_id}', [RequestController::class, 'approvedView'])->name('approved.view');
    Route::post('stamp_correction_request/approved/{attendance_correct_request_id}', [RequestController::class, 'approved'])->name('admin.approved');
    Route::get('/admin/staff/{user_id}/export', [AdminListController::class, 'export'])->name('admin.staff.export');
});