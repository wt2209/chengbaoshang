<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    protected $fillable = ['title', 'has_rent', 'remark'];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function records()
    {
        return $this->hasMany(Record::class);
    }
}
