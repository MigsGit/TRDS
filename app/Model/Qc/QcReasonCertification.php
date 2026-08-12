<?php

namespace App\Model\Qc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Model\DropdownMasterDetail;

class QcReasonCertification extends Model
{
    use HasFactory;

      public function dropdown_reason()
    {
        return $this->belongsTo(DropdownMasterDetail::class, 'reason_of_certification', 'id');
    }
    protected $fillable = [
        'qc_slips_id',
        'reason_of_certification',
        'transfer_flexibility',
        'others',
    ];
}
