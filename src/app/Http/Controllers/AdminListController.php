<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Response;
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

    public function export($user_id, Request $request)
    {
        $user = User::findOrFail($user_id);

        $month = $request->input('month', now()->format('Y-m'));
        $carbonMonth = Carbon::createFromFormat('Y-m', $month);

        $startOfMonth = $carbonMonth->copy()->startOfMonth();
        $endOfMonth = $carbonMonth->copy()->endOfMonth();

        $attendances = $user->attendances()
            ->with('breakTimes')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $dates = CarbonPeriod::create($startOfMonth, $endOfMonth);

        $filename = "{$user->name}_attendance_" . $carbonMonth->format('Y-m') . '.csv';

        $handle = fopen('php://temp', 'r+');
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($handle, ['日付', '出勤', '退勤', '休憩', '合計']);

        foreach ($dates as $date) {
            $attendance = $attendances->get($date->format('Y-m-d'));

            if ($attendance) {
                $clockIn = optional($attendance->clock_in)->format('H:i');
                $clockOut = optional($attendance->clock_out)->format('H:i');
                $break = gmdate('H:i', $attendance->total_break_minutes * 60);
                $work = gmdate('H:i', $attendance->work_time * 60);
            } else {
                $clockIn = $clockOut = $break = $work = '-';
            }

            fputcsv($handle, [
                $date->isoFormat('M月D日(ddd)'),
                $clockIn,
                $clockOut,
                $break,
                $work
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv)
            ->withHeaders([
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename={$filename}",
            ]);
    }
}
