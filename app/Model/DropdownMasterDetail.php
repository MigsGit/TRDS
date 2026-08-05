<?php

namespace App\Model;

use App\Model\Qc\CLqcTrainingItemResult;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DropdownMasterDetail extends Model
{
    public function dropdown_master()
    {
        return $this->hasOne(DropdownMaster::class, 'id', 'dropdown_masters_id');
    }
    public function c_lqc_training_item_results()
    {
        return $this->hasMany(CLqcTrainingItemResult::class,'training_item_id');
    }
}
