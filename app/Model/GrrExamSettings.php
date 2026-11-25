<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrrExamSettings extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'grr_setting_id',
        'ctrl_number',
        'section',
        'emp_id',
        'description',
        'purpose',
        'instructions',
        'status'
    ];

    public function grrSetting(){
        return $this->belongsTo(GrrSettings::class, 'grr_setting_id');
    }

    public function examEmployees(){
        return $this->hasMany(GrrExamEmployees::class, 'exam_grouping_id')->where('exam_status', '!=', 2);
    }
}
