<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UtilityCharge extends Model
{
    protected $fillable = [
        'room_id', 'electric_amount', 'electric_money', 
        'water_amount', 'water_money', 'charged_at', 
        'charger', 'charger_phone'
    ];
}
