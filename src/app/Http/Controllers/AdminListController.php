<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\CarbonPeriod;
use App\Models\Attendance;


class AdminListController extends Controller
{
    public function listView(Request $request)
    {
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

        return view('admin.list', compact('today', 'carbonDay', 'currentDay', 'users'));
    }

    public function staffListView()
    {
        $users = User::where('role', 'user')->get();

        return view('admin.staff', compact('users'));
    }

    public function attendanceList($user_id, Request $request)
    {
        $user = User::find($user_id);

        $month = $request->input('month', now()->format('Y-m'));

        $carbonMonth = Carbon::createFromFormat('Y-m', $month);

        $currentMonth = $carbonMonth->format('Y/n');

        $startOfMonth = $carbonMonth->copy()->startOfMonth();
        $endOfMonth = $carbonMonth->copy()->endOfMonth();

        $attendances = $user->attendances()->with('breaktimes')->whereBetWeen('date', [$startOfMonth, $endOfMonth])->orderBy('date', 'asc')->get()->keyBy('date');

        $dates = CarbonPeriod::create($startOfMonth,    $endOfMonth);

        return view('admin.staff_attendance', compact('user', 'currentMonth', 'attendances', 'dates', 'carbonMonth'));
    }
}
