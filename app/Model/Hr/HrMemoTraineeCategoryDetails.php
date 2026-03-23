<?php

namespace App\Model\Hr;

use Illuminate\Database\Eloquent\Model;
use App\Model\Hr\HrMemoExamination;

class HrMemoTraineeCategoryDetails extends Model
{
    public function exam_info()
    {
        return $this->hasMany(HrMemoExamination::class, 'id', 'category');
    }
}
