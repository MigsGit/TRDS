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
        'day_number',   
        'result',
        'item_remark',
        'sub_description',
        'date',
    ];

    public function dropdown_master_details()
    {
        return $this->belongsTo(DropdownMasterDetail::class, 'id', 'training_item_id');
    }
}
