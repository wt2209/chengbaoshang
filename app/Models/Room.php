<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'area', 'title', 'building', 'unit', 'default_number',
        'default_deposit', 'default_rent', 'remark'
    ];

    public function category()
    {
        return $this->belongsTo(Room::class);
    }

    public function records()
    {
        return $this->hasMany(Record::class);
    }
}
