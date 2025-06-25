<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'clock_in',
        'clock_out',
        'status',
        'note',
    ];

    public function getStatusLabelAttribute()
    {
        switch ($this->status) {
            case 'waiting':
                return '承認待ち';
            case 'approved':
                return '承認済み';
        }
    }

    public function attendance()
    {
        return $this->belonsTo(Attendance::class);
    }

    public function breakTimeRequests()
    {
        return $this->hasMany(BreakTimeRequest::class);
    }
}
