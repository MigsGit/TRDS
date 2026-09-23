<?php

namespace App\Model;

use App\Model\DropdownMasterDetail;
use App\Model\SystemOneHrisSubcon;
use App\Model\TrainingRecordEmployee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingRecord extends Model
{
    use HasFactory;

    protected $table = 'training_records';
    protected $fillable = [
        'start_date',
        'end_date',
        'training_title',
        'objective',
        'remarks',
        'trainer',
        'venue',
        'type_of_training',
        'attachments',
        'created_by',
        'updated_by',
    ];
    protected $appends = ['trainer_details'];

    // 2. Change the method name to get[Name]Attribute
    public function getTrainerDetailsAttribute()
    {
        if (empty($this->trainer)) {
            return collect();
        } 

        $trainerIds = explode(',', $this->trainer);
        return SystemOneHrisSubcon::whereIn('EmpNo', $trainerIds)->get();
    }

    public function venue_details(){
        return $this->hasOne(DropdownMasterDetail::class, 'id', 'venue');
    }

    public function trainee_details(){
        return $this->hasMany(TrainingRecordEmployee::class, 'training_record_id', 'id');
    }
}
