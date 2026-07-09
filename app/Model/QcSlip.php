<?php

namespace App\Model;

use App\Model\DropdownMasterDetail;
use App\OpApprover;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QcSlip extends Model
{
    use HasFactory;

   
    public function product_line()
    {
        return $this->hasOne(DropdownMasterDetail::class, 'id', 'product_line');
    }
    public function op_approvers()
    {
        return $this->hasOne(OpApprover::class, 'qc_slips_id', 'id');
    }
}
