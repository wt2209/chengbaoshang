<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $fillable = [
        'company_id', 'location', 'type', 'money', 'description',
        'remark', 'charged_at', 'charge_way', 'charger',
    ];

    protected $casts = [
        'created_at' => 'date:Y-m-d'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
