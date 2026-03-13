<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemOneSubconEmpInfo extends Model
{
    protected $table = 'vw_employeeinfo';
    protected $connection = 'mysql_subcon';
}
