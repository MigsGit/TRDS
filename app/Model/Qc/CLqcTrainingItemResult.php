<?php

namespace App\Model\Qc;

use App\Model\DropdownMasterDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CLqcTrainingItemResult extends Model
{
    use HasFactory;

    /**
     * Get the user associated with the CLqcTrainingItemResult
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    protected $fillable =[
        'qc_slips_id',
        'training_item_id',
        'position',
        'day_number',   
        'result',
        'item_remark',
        'is_checked',
        'sub_description',
        'date',
        'trainer_emp_no',
        'trainer_name',
        'validation_date',
        'validation_time',
        'overall_result',
        'chk_trainer_emp_no',
        'chk_trainer_name',
        'chk_validation_date',
        'chk_validation_time',
    ];

    public function dropdown_master_details()
    {
        return $this->belongsTo(DropdownMasterDetail::class, 'id', 'training_item_id');
    }
}
