<?php

namespace App\Model;

use App\Model\DropdownMasterDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QcSlip extends Model
{
    use HasFactory;

   
    public function product_line()
    {
        return $this->hasOne(DropdownMasterDetail::class, 'id', 'product_line');
    }
}
