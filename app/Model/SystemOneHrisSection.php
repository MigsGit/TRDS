<?php

namespace App\Model;

use App\Model\SystemOneHrisDepartment;
use Illuminate\Database\Eloquent\Model;

class SystemOneHrisSection extends Model
{
    protected $table = 'tbl_Section';
    protected $connection = 'mysql_systemone';

    public function department()
    {
        return $this->hasOne(SystemOneHrisDepartment::class, 'pkid', 'fkDepartment');
    }
}
