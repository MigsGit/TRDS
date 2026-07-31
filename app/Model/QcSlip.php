<?php

namespace App\Model;

use App\Model\DropdownMasterDetail;
use App\Model\Qc\AOperProdTrainingOrientation;
use App\Model\Qc\BOpEnggSectionTrainingOrientation;
use App\Model\Qc\CQcCertification;
use App\Model\Qc\DPpdCertificationCompletion;
use App\Model\Qc\EQcValidationProcess;
use App\Model\Qc\FQcValidation;
use App\Model\Qc\QcReasonCertification;
use App\Model\Qc\QcSlipEmployee;
use App\Model\RapidXUser;
use App\Model\Qc\OpApprover;
use App\OpApprover;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QcSlip extends Model
{
    use HasFactory;
    protected $table = 'qc_slips';

    protected $fillable = [
        'status'
    ];
    public function product_line_details() // Chris
    {
        return $this->hasOne(DropdownMasterDetail::class, 'id', 'product_line');
    }
    // public function product_line()
    // {
    //     return $this->hasOne(DropdownMasterDetail::class, 'id', 'product_line');
    // }
    public function system_one_hris_subcon()
    {
        return $this->hasMany(SystemOneHrisSubcon::class, 'EmpNo',  'created_by');
    }
    public function op_approvers()
    {
        return $this->hasMany(OpApprover::class, 'qc_slips_id',  'id')->whereNull('deleted_at');
    }
    public function op_approvers_pending()
    {
        return $this->hasMany(OpApprover::class, 'qc_slips_id', 'id')
        ->where('decision_status','PEN')
        ->whereNull('deleted_at');
    }
    public function qc_slip_employees()
    {
        return $this->hasMany(QcSlipEmployee::class, 'qc_slips_id',  'id')->whereNull('deleted_at');
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
        return $this->hasOne(QcReasonCertification::class, 'qc_slips_id',  'id')->where('deleted_at');
    }
    public function a_oper_prod_training_orientation()
    {
        return $this->hasOne(AOperProdTrainingOrientation::class, 'qc_slips_id',  'id')->where('deleted_at');
    }
    public function b_op_engg_section_training_orientation()
    {
        return $this->hasOne(BOpEnggSectionTrainingOrientation::class, 'qc_slips_id',  'id')->where('deleted_at');
    }
    public function c_qc_certification()
    {
        return $this->hasOne(CQcCertification::class, 'qc_slips_id',  'id')->where('deleted_at');
    }
    public function d_ppd_certification_completion()
    {
        return $this->hasOne(DPpdCertificationCompletion::class, 'qc_slips_id',  'id')->where('deleted_at');
    }
    public function e_qc_validation_process()
    {
        return $this->hasOne(EQcValidationProcess::class, 'qc_slips_id',  'id')->where('deleted_at');
    }
    public function f_qc_validation()
    {
        return $this->hasOne(FQcValidation::class, 'qc_slips_id',  'id')->where('deleted_at');
    }

    public function productLine()
    {
        return $this->belongsTo(DropdownMasterDetail::class, 'product_line', 'id');
    }

}
