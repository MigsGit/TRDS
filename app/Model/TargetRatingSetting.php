<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Model\TargetRatingDetails;

class TargetRatingSetting extends Model
{
    use HasFactory;

    protected $table = 'target_rating_settings';

    public function rating_details(){
        return $this->hasMany(TargetRatingDetails::class, 'target_rating_id', 'id');
    }

    public function product_line_info(){
        return $this->hasOne(DropdownMasterDetail::class, 'id', 'product_line'); //ID from DropdownMaster for operator station
    }
}
