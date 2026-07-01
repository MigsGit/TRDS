<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SystemOneHrisDepartment extends Model
{
    protected $table = 'tbl_Department';
    protected $connection = 'mysql_systemone';
}
