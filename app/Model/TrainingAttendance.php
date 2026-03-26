<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_request_details_id',
        'rapidx_emp_no',
        'date',
        'time_in',
        'time_out',
        'status',
        'remarks'
    ];
}
