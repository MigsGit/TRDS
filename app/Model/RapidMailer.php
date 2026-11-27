<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RapidMailer extends Model
{
    protected $connection = "mysql_rapid_auto_mailer";
    protected $table = "tbl_mailer";
}
