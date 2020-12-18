<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rent extends Model
{
    protected $fillable = [
        'record_id', 'company_name', 'money', 'year', 'month', 
        'start_date', 'end_date', 'charged_at', 'is_refund'
    ];
    
    public function record()
    {
        return $this->belongsTo(Record::class);
    }
}
