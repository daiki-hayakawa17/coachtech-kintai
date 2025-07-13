<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\AttendanceCorrectRequest;
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

        $attendance = Attendance::with('breaktimes', 'attendanceRequests')->find($attendance_id);
        $breaktimes = $attendance->breaktimes->take(2);
        $attendance_user = $attendance->user;

        $attendanceRequest = $attendance->attendanceRequests()->latest()->first();

        return view('detail', compact('attendance', 'user', 'statusLabel', 'breaktimes', 'attendance_user', 'layout', 'attendanceRequest'));
    }

    public function storeRequest($attendance_id, Request $request)
    {
        $user = Auth::user();

        $attendance = Attendance::with('breakTimes')->find($attendance_id);

        $breakTime1 = $attendance->breakTimes->first();
        $breakTime2 = $attendance->breakTimes->get(1);

        if ($user->role === 'user') {
            $attendanceRequest = AttendanceCorrectRequest::create([
                'attendance_id' => $attendance_id,
                'clock_in' => Carbon::parse($attendance->date . ' ' . $request->clock_in),
                'clock_out' => Carbon::parse($attendance->date . ' ' . $request->clock_out),
                'status' => 'waiting',
                'note' => $request->note,
            ]);

            $attendanceRequest->breakTimeRequests()->create([
                'attendance_correct_request_id' => $attendanceRequest->id,
                'break_in' => Carbon::parse($attendance->date . ' ' .$request->break_in[0]),
                'break_out' => Carbon::parse($attendance->date . ' ' . $request->break_out[0]),
            ]);

            if (!empty($request->break_in[1]) && !empty($request->break_out[1])) {
                $attendanceRequest->breakTimeRequests()->create([
                    'attendance_correct_request_id' => $attendanceRequest->id,
                    'break_in' => Carbon::parse($attendance->date . ' ' .$request->break_in[1]),
                    'break_out' => Carbon::parse($attendance->date . ' ' . $request->break_out[1]),
                ]);
            }
        }

        if ($user->role === 'admin') {
            $attendance->update([
                'clock_in' => Carbon::parse($attendance->date . ' ' . $request->clock_in),
                'clock_out' => Carbon::parse($attendance->date . ' ' . $request->clock_out),
            ]);

            if ($request->break_in[0] && $request->break_out[0]) {
                if ($breakTime1) {
                    $breakTime1->update([
                        'break_in' => Carbon::parse($attendance->date . ' ' . $request->break_in[0]),
                        'break_out' => Carbon::parse($attendance->date . ' ' . $request->break_out[0]),
                    ]);
                } else {
                    $attendance->breakTimes()->create([
                        'break_in' => Carbon::parse($attendance->date . ' ' . $request->break_in[0]),
                        'break_out' => Carbon::parse($attendance->date . ' ' . $request->break_out[0]),
                    ]);
                }
            }

            if ($request->break_in[1] && $request->break_out[1]) {
                if ($breakTime2) {
                    $breakTime2->update([
                        'break_in' => Carbon::parse($attendance->date . ' ' . $request->break_in[1]),
                        'break_out' => Carbon::parse($attendance->date . ' ' . $request->break_out[1]),
                    ]);
                } else {
                    $attendance->breakTimes()->create([
                        'break_in' => Carbon::parse($attendance->date . ' ' . $request->break_in[1]),
                        'break_out' => Carbon::parse($attendance->date . ' ' . $request->break_out[1]),
                    ]);
                }
            }
        }
        
    
        return redirect()->route('attendance.detail', ['attendance_id' => $attendance_id]);
    }

    public function listView()
    {
        $user = Auth::user();
        $page = request()->query('page', 'waiting');
        $statusLabel = $this->getTodayStatusLabel();

        $query = AttendanceCorrectRequest::with(['breakTimeRequests', 'attendance'])->where('status', $page);

        if ($user->role === 'user') {
            $query->whereHas('attendance', function ($attendanceQuery) use ($user) {
                $attendanceQuery->where('user_id', $user->id);
            });

            $attendanceRequests = $query->get();

            return view('request', compact('attendanceRequests', 'statusLabel', 'user'));
        }

        $attendanceRequests = $query->get();

        return view('admin.request', compact('attendanceRequests', 'statusLabel', 'user'));
    }

    public function approvedView($attendance_correct_request_id)
    {
        $attendanceRequest = AttendanceCorrectRequest::with('attendance','breakTimeRequests')->find($attendance_correct_request_id);
        $attendance = $attendanceRequest->attendance;
        $breakTimeRequests = $attendanceRequest->breakTimeRequests->take(2);
        $user = $attendance->user;

        return view('admin.approved', compact('attendance', 'breakTimeRequests', 'user', 'attendanceRequest'));
    }

    public function approved($attendance_correct_request_id, Request $request)
    {
        $attendanceRequest = AttendanceCorrectRequest::with('attendance','breakTimeRequests')->find($attendance_correct_request_id);
        $breakTimeRequest1 = $attendanceRequest->breakTimeRequests->first();
        $breakTimeRequest2 = $attendanceRequest->breakTimeRequests->get(1);

        $attendance = $attendanceRequest->attendance;
        $breakTime1 = $attendance->breakTimes->first();
        $breakTime2 = $attendance->breakTimes->get(1);

        $attendance->update([
            'clock_in' => $attendanceRequest->clock_in,
            'clock_out' => $attendanceRequest->clock_out
        ]);

        if ($breakTimeRequest1) {
            if ($breakTime1) {
                $breakTime1->update([
                    'break_in' => $breakTimeRequest1->break_in,
                    'break_out' => $breakTimeRequest1->break_out,
                ]);
            } else {
                $attendance->breakTimes()->create([
                    'break_in' => $breakTimeRequest1->break_in,
                    'break_out' => $breakTimeRequest1->break_out,
                ]);
            }
        }

        if ($breakTimeRequest2) {
            if ($breakTime2) {
                $breakTime2->update([
                    'break_in' => $breakTimeRequest2->break_in,
                    'break_out' => $breakTimeRequest2->break_out,
                ]);
            } else {
                $attendance->breakTimes()->create([
                    'break_in' => $breakTimeRequest2->break_in,
                    'break_out' => $breakTimeRequest2->break_out,
                ]);
            }
        }

        $attendanceRequest->update([
            'status' => 'approved',
        ]);
        
        return redirect()->route('request.list');
    }
}
