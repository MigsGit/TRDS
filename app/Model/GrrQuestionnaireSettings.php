<?php

namespace App\Model;
use App\Model\GrrSettings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrrQuestionnaireSettings extends Model
{
    use HasFactory;

    public function grrSample(){
        return $this->hasMany(GrrSettings::class, 'id', 'grr_setting_id');
    }

    public function scopeOrdered($query){
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
