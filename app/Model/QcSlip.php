<?php

namespace App\Model;

use App\Model\DropdownMasterDetail;
use App\Model\Qc\AOperProdTrainingOrientation;
use App\Model\Qc\QcReasonCertification;
use App\Model\Qc\QcSlipEmployee;
use App\OpApprover;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QcSlip extends Model
{
    use HasFactory;

    protected $fillable = [
        'status'
    ];
    // public function product_line()
    // {
    //     return $this->hasOne(DropdownMasterDetail::class, 'id', 'product_line');
    // }
    public function op_approvers()
    {
        return $this->hasMany(OpApprover::class, 'qc_slips_id', 'id');
    }
    public function qc_slip_employees()
    {
        return $this->hasMany(QcSlipEmployee::class, 'qc_slips_id', 'id');
    }
    public function dropdown_master_detail($column)
    {
        return $this->hasOne(DropdownMasterDetail::class, 'id', $column);
    }
    public function product_line()
    {
       return $this->dropdown_master_detail('product_line');
    }
    public function qc_reason_certification()
    {
        return $this->hasOne(QcReasonCertification::class, 'qc_slips_id', 'id');
    }
    public function a_oper_prod_training_orientation()
    {
        return $this->hasOne(AOperProdTrainingOrientation::class, 'qc_slips_id', 'id');
    }
    
}
