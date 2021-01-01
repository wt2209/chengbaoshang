<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $fillable = [
        'record_id', 'money', 'billed_at', 'refunded_at', 'refund_company_name', 'charged_at'
    ];

    public function record()
    {
        return $this->belongsTo(Record::class);
    }
}
