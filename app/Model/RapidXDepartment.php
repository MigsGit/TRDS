<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RapidXDepartment extends Model
{
    use HasFactory;
    protected $table = 'departments';
    protected $connection = 'rapidx';
}
