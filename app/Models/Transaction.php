<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $casts = ['created_at' => 'date'];

    public function getDateAttribute()
    {
        return $this->created_at->format('m/d/Y');
    }

    public function setDateAttribute($value)
    {
        $this->created_at = new Carbon($value);
    }
}
