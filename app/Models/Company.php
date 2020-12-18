<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'category_id', 'company_name', 'manager', 'manager_phone', 
        'linkman', 'linkman_phone', 'remark'
    ];

    public function records()
    {
        return $this->hasMany(Record::class);
    }
}
