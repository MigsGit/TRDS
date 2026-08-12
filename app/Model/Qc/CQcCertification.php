<?php

namespace App\Model\Qc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CQcCertification extends Model
{
    use HasFactory;

    protected $fillable = [
        'qc_slips_id',
        'obs_first_result_qcs_oper',
        'obs_second_result_qcs_oper',
        'first_sample_qcs_oper',
        'second_sample_qcs_oper',
        'first_ok_qcs_oper',
        'first_ng_qcs_oper',
        'second_ok_qcs_oper',
        'second_ng_qcs_oper',
        'updated_by',
        'qcs_station_1st_oper',
        'qcs_station_2nd_oper',
    ];
}
