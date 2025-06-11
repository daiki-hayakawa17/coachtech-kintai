<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use App\Models\Attendance;
use App\Models\BreakTime;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userId = 1;

        $startDate = Carbon::now()->subMonth()->startOfMonth();
        $endDate = Carbon::today();

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            if (rand(1,100) <= 30) {
                continue;
            }

            $clockIn = $date->copy()->setTime(9,0);
            $clockOut = $date->copy()->setTime(18, 0);

            $breakIn = $date->copy()->setTime(12,0);
            $breakOut = $date->copy()->setTime(13,0);
            $breakMinutes = $breakIn->diffInMinutes($breakOut);

            $workMinutes = $clockIn->diffInMinutes($clockOut) - $breakMinutes;

            $attendance = Attendance::create([
                'user_id' => $userId,
                'date' => $date->toDateString(),
                'clock_in' => $clockIn,
                'clock_out' => $clockOut,
                'work_time' => $workMinutes,
                'status' => 'done',
            ]);

            BreakTime::create([
                'attendance_id' => $attendance->id,
                'break_in' => $breakIn,
                'break_out' => $breakOut,
                'break_time' => $breakMinutes,
            ]);
        }
    }
}
