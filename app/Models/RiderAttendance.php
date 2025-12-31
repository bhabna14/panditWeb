<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiderAttendance extends Model
{
    use HasFactory;

    protected $table = 'rider_attendances';

    protected $fillable = [
        'rider_id',
        'attendance_date',
        'status',
        'check_in_time',
        'check_out_time',
        'working_minutes',
        'remarks',
        'marked_by',
    ];

    protected $casts = [
        'attendance_date' => 'date',
    ];
    

    public function rider()
    {
        return $this->belongsTo(RiderDetails::class, 'rider_id', 'rider_id');
    }
}
