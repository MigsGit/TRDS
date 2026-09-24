<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DropdownMaster extends Model
{
     /**
     * Get DropdownMaster Details
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    protected $fillable = [
        'status',
        'dropdown_master',
        'category',
        'remarks',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
    ];
    
    public function dropdown_master_details()
    {
        return $this->hasMany(DropdownMasterDetail::class, 'dropdown_masters_id', 'id');
    }
}
