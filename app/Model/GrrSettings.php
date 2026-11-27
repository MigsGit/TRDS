<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrrSettings extends Model
{
    use HasFactory;

    public const SECTIONS = ['TS-F1', 'TS-F3', 'CN', 'CN-F3', 'PPD-CN', 'PPD-TS', 'PPD-F3', 'YF'];

    public function GrrExamSettings(){
        return $this->hasMany(GrrExamSettings::class, 'grr_setting_id', 'id');
    }
}
