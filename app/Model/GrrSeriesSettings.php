<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class GrrSeriesSettings extends Model
{
    protected $table = 'grr_series_settings';

    protected $fillable = ['section', 'series_name', 'status'];
}
