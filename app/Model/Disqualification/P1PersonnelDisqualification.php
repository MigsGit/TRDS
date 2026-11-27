<?php

namespace App\Model\Disqualification;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Model\DropdownMaster;
use App\Model\DropdownMasterDetail;
use App\RapidXUser;

use App\Model\Qc\QcSlip;
use App\Model\Qc\QcSlipEmployee;

use App\Model\Disqualification\P1EmailRecipients;
use App\Model\Disqualification\P2CounselingReorientation;
use App\Model\Disqualification\P2Reorientation;


class P1PersonnelDisqualification extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'p1_personnel_disqualifications';

    // Used by the simple Disqualification Record CRUD module (DisqualificationRecordController).
    protected $fillable = [
        'status',
        'control_no',
        'disqualification_date',
        'recertification_date',
        'disqualified_personnel',
        'station',
        'disqualification_reason',
        'mode_of_defect',
        'series_name',
        'classification',
        'c1_subclass',
        'created_by',
        'last_updated_by',
    ];

    // protected $casts = [
    //     'disqualification_date' => 'date',
    //     'recertification_date' => 'date',
    // ];

    public function email_recipients(){
        return $this->hasMany(P1EmailRecipients::class, 'p1_id', 'id');
    }

    public function disqualified_personnel_info(){
        return $this->hasOne(QcSlipEmployee::class, 'employee_no', 'disqualified_personnel');
    }

    public function classifications(){
        return $this->hasOne(DropdownMaster::class, 'id', 'classification');
    }

    public function subclassifications(){
        return $this->hasOne(DropdownMaster::class, 'id', 'c1_subclass');
    }

    public function station_name(){
        return $this->hasOne(DropdownMasterDetail::class, 'id', 'station'); //ID from DropdownMaster for operator station
    }

    public function part2_info(){
        return $this->hasOne(P2CounselingReorientation::class, 'p1_id', 'id');
    }

    public function created_by_info(){
        return $this->hasOne(RapidXUser::class, 'id', 'created_by');
    }

    public function last_updated_by_info(){
        return $this->hasOne(RapidXUser::class, 'id', 'last_updated_by');
    }
}
