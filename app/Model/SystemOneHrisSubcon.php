<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemOneHrisSubcon extends Model
{

    protected $table = 'vw_pmi_subcon_hris'; //ALL EMPLOYEEE
    protected $connection = 'mysql_hris_subcon';
}
