<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    protected $fillable = [
        'category_id', 'room_id', 'company_id', 'gender', 
        'company_name', 'rent', 'is_living', 'entered_at', 
        'quit_at', 'has_lease', 'lease_start', 'lease_end',
         'electric_start_base', 'electric_end_base', 
         'water_start_base', 'water_end_base'
    ];


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * 每条入住记录含有多条租金记录
     */
    public function rents()
    {
        return $this->hasMany(Rent::class);
    }

    /**
     * 每一条入住记录只有一条押金记录
     */
    public function deposit()
    {
        return $this->hasOne(Deposit::class);
    }
}
