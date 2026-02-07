<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'field_id', 'user_name', 'deposit', 'start_time', 
        'end_time', 'booking_date', 'is_constant', 'day_of_week'
    ];

    protected $casts = [
        'is_constant' => 'boolean',
    ];

    public function field()
    {
        return $this->belongsTo(Field::class);
    }
}