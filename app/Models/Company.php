<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'category_id', 'company_name', 'manager', 'manager_phone', 
        'linkman', 'linkman_phone', 'remark'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function records()
    {
        return $this->hasMany(Record::class);
    }

    public function renames()
    {
        return $this->hasMany(Rename::class);
    }
}
