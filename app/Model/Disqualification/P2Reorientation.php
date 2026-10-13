<?php

namespace App\Model\Disqualification;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Model\SystemOneHrisEmpInfo;
use App\Model\SystemOneSubconEmpInfo;
use App\Model\SystemOneHrisSubcon;
use App\Model\DropdownMaster;
use App\RapidXUser;

class P2Reorientation extends Model
{
    use HasFactory;

    public function scope_info(){
        return $this->hasOne(DropdownMaster::class, 'id', 'training_scope');
    }

    // public function trained_by_info(){
    //     return $this->hasOne(RapidXUser::class, 'id', 'created_by');
    // }

    public function trained_by_info(){
        return $this->hasOne(SystemOneHrisSubcon::class, 'EmpNo', 'trained_by');
    }

    public function created_by_info(){
        return $this->hasOne(RapidXUser::class, 'id', 'created_by');
    }

    public function last_updated_by_info(){
        return $this->hasOne(RapidXUser::class, 'id', 'last_updated_by');
    }
}
