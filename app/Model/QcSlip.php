<?php

namespace App\Model;

use App\OpApprover;
use App\Model\DropdownMasterDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QcSlip extends Model
{
    use HasFactory;

    public function product_line_details(){ // Model Chris
        return $this->hasOne(DropdownMasterDetail::class, 'id', 'product_line');
    }

    public function op_approver_details(){ // Model Chris
        return $this->hasMany(OpApprover::class, 'qc_slips_id',  'id')->whereNull('deleted_at');
    }
}
