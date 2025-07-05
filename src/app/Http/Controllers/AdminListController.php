<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Attendance;

class AdminListController extends Controller
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

    public function listView(Request $request)
    {
        $statusLabel = $this->getTodayStatusLabel();

        $today = Carbon::now()->isoFormat('YYYY年M月D日');
        $day = $request->input('day', now()->format('Y-m-d'));

        $carbonDay = Carbon::createFromFormat('Y-m-d', $day);
        $currentDay = $carbonDay;

        $users = User::where('role', 'user')->whereHas('attendances', function ($query) use ($day) {
            $query->where('date', $day);
        })
        ->with(['attendances' => function ($query) use ($day) {
            $query->where('date', $day);
        }, 'attendances.breaktimes'])->get();

        return view('admin.list', compact('today', 'statusLabel', 'carbonDay', 'currentDay', 'users'));
    }
}
