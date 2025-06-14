<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BreakTime extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'break_in',
        'break_out',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function getDurationAttribute()
    {
        if (!$this->break_in || !$this->break_out) {
            return 0;
        }

        return Carbon::parse($this->break_out)->diffInMinutes(Carbon::parse($this->break_in));
    }
}
