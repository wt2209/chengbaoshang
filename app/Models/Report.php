<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'record_id', 'company_id', 'room_id', 'company_name', 'start_date', 'end_date',
        'year', 'month', 'pre_electric_base', 'current_electric_base',
        'electric_amount', 'electric_price', 'electric_money',
        'pre_water_base', 'current_water_base', 'water_amount',
        'water_money', 'rent', 'rent_discount', 'actual_rent', 'charged_at'
    ];

    public function record()
    {
        return $this->belongsTo(Record::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
