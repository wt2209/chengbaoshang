<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UtilityBase extends Model
{
    protected $fillable = [
        'room_id', 'pre_electric_base', 'current_electric_base', 
        'pre_water_base', 'current_water_base', 'year', 'month'
    ];
}
