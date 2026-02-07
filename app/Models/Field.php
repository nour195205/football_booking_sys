<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    protected $fillable = ['name', 'description'];

    // علاقة الملعب بالأسعار
    public function prices()
    {
        return $this->hasMany(FieldPrice::class);
    }

    // علاقة الملعب بالحجوزات
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}