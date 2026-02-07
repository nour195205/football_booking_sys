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

    public function payments()
{
    return $this->hasMany(Payment::class);
}

    // دالة سريعة لحساب إجمالي ما تم دفعه لهذا الحجز حتى الآن
    public function getTotalPaidAttribute() {
        return $this->payments->sum('amount');
    }
}