<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rename extends Model
{
    protected $fillable = ['company_id', 'old_name', 'new_name', 'renamed_at'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
