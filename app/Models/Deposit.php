<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
    ];

    protected $fillable = [
        'record_id', 'company_id', 'room_id',  'money', 'billed_at',
        'refunded_at', 'refund_company_name', 'charged_at',
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
