<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreakTimeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_request_id',
        'break_in',
        'break_out',
    ];

    public function AttendanceRequest()
    {
        return $this->belongsTo(AttendanceRequest::class);
    }
}
