<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    protected $fillable = ['title', 'has_rent', 'remark'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function records()
    {
        return $this->hasMany(Record::class);
    }
}
