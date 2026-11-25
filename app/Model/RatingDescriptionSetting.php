<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Model\RatingDetails;

class RatingDescriptionSetting extends Model
{
    use HasFactory;

    public function rating_description_details(){
        return $this->hasMany(RatingDetails::class, 'rating_description_id', 'id');
    }
}
