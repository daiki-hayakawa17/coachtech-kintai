<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\AttendanceRequest;
use App\Models\BreakTimeRequest;


class RequestController extends Controller
{
    public function getTodayStatusLabel()
    {
        $user = Auth::user();
        $attendance = $user->attendances()->whereDate('clock_in', today())->first();

        if ($attendance) {
            return $attendance->status_label;
        }else {
            return '勤務外';
        }
    }

    public function detail($attendance_id)
    {
        $user = Auth::user();
        $statusLabel = $this->getTodayStatusLabel();
        $layout = Auth::user()->role === 'admin' ? 'layouts.admin.app' : 'layouts.app';

        $attendance = Attendance::with('breaktimes', 'attendanceRequest')->find($attendance_id);
        $breaktimes = $attendance->breaktimes->take(2);
        $attendance_user = $attendance->user;

        return view('detail', compact('attendance', 'user', 'statusLabel', 'breaktimes', 'attendance_user', 'layout'));
    }

    public function storeRequest($attendance_id, Request $request)
    {
        $user = Auth::user();

        $attendance = Attendance::find($attendance_id);

        $attendanceRequest = AttendanceRequest::create([
            'attendance_id' => $attendance_id,
            'clock_in' => Carbon::parse($attendance->date . ' ' . $request->clock_in),
            'clock_out' => Carbon::parse($attendance->date . ' ' . $request->clock_out),
            'status' => 'waiting',
            'note' => $request->note,
        ]);

        $attendanceRequest->breakTimeRequests()->create([
            'attendance_request_id' => $attendanceRequest->id,
            'break_in' => Carbon::parse($attendance->date . ' ' .$request->break_in[0]),
            'break_out' => Carbon::parse($attendance->date . ' ' . $request->break_out[0]),
        ]);

        if (!empty($request->break_in[1]) && !empty($request->break_out[1])) {
            $attendanceRequest->breakTimeRequests()->create([
                'attendance_request_id' => $attendanceRequest->id,
                'break_in' => Carbon::parse($attendance->date . ' ' .$request->break_in[1]),
                'break_out' => Carbon::parse($attendance->date . ' ' . $request->break_out[1]),
            ]);
        }
    
        return redirect()->route('attendance.detail', ['attendance_id' => $attendance_id]);
    }

    public function listView()
    {
        $user = Auth::user();
        $page = request()->query('page', 'waiting');
        $statusLabel = $this->getTodayStatusLabel();

        if ($page === 'waiting') {
            $attendanceRequests = AttendanceRequest::with('breakTimeRequests', 'attendance')->where('status', 'waiting')->get();
        } elseif ($page === 'approved') {
            $attendanceRequests = AttendanceRequest::with('breakTimeRequests', 'attendance')->where('status', 'approved')->get();
        }

        return view('request', compact('attendanceRequests', 'statusLabel', 'user'));
    }
}
