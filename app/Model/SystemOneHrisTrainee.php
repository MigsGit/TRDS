<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Model\SystemOneHrisTraining;

class SystemOneHrisTrainee extends Model
{
    protected $table = 'tbl_Trainee';
    protected $connection = 'mysql_systemone';

    public function employee_training_record_info(){
        return $this->belongsTo(SystemOneHrisTraining::class, 'fkTraining', 'pkid');
    }
}
