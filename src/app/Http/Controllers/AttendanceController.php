<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function attendanceView()
    {
        $user = Auth::user();
        $now = Carbon::now()->format('H:i');
        $today = Carbon::now()->isoFormat('YYYY年M月D日 (ddd) ');
        $date = Carbon::now()->format('Y-m-d');
        $attendance = $user->attendances()->whereDate('clock_in', today())->first();

        if ($attendance) {
            $statusLabel = $attendance->status_label;
        } else {
            $statusLabel = '勤務外';
        }

        // dd($status);
        return view('attendance', compact('now', 'today', 'statusLabel', 'date'));
    }

    public function updateStatus(Request $request)
    {
        $user = Auth::user();
        $attendance = $user->attendances()->whereDate('clock_in', today())->first();
        $date = $request->input('date');

        $now = Carbon::now();

        switch ($request->action_type) {
            case 'clock_in':
                if (!$attendance) {
                    $user->attendances()->create([
                        'clock_in' => $now,
                        'status' => 'working',
                        'date' => $date,
                    ]);
                }
                break;

            case 'clock_out':
                if ($attendance && !$attendance->clock_out) {
                    $attendance->update([
                        'clock_out' => $now,
                        'status' => 'done',
                    ]);
                }
                break;

            case 'break_in':
                if ($attendance) {
                    $attendance->breakTimes()->create([
                        'break_in' => $now,
                    ]);
                    $attendance->update(['status' => 'break']);
                }
                break;
            
            case 'break_out':
                if ($attendance) {
                    $break = $attendance->breakTimes()->whereNull('break_out')->latest()->first();
                    if ($break) {
                        $break->update(['break_out' => $now]);
                    }
                    $attendance->update(['status' => 'working']);
                }
                break;
        }

        return redirect()->route('attendance.view');
    }
}
