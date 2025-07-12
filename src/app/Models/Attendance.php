<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'clock_in',
        'clock_out',
        'status',
    ];

    public function breakTimes()
    {
        return $this->hasMany(BreakTime::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendanceRequests()
    {
        return $this->hasMany(AttendanceCorrectRequest::class);
    }

    public function getStatusLabelAttribute()
    {
        switch ($this->status) {
            case 'working':
                return '出勤中';
            case 'breaking':
                return '休憩中';
            case 'done':
                return '退勤済';
        }
    }

    public function getTotalBreakMinutesAttribute()
    {
        return $this->breakTimes->sum('duration');
    }

    public function getWorkTimeAttribute()
    {
        if (!$this->clock_in || !$this->clock_out) {
            return 0;
        }

        $total = Carbon::parse($this->clock_out)->diffInMinutes(Carbon::parse($this->clock_in));

        return $total - $this->total_break_minutes;
    }

    public function getFormattedWorkTimeAttribute()
    {
        $minutes = $this->work_time;
        return sprintf('%02d:%02d', floor($minutes / 60), $minutes % 60);
    }

    public function getFormattedBreakTimeAttribute()
    {
        $minutes = $this->total_break_minutes;
        return sprintf('%02d:%02d', floor($minutes /60), $minutes % 60);
    }
}
