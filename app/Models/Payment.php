<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['booking_id', 'amount', 'paid_at', 'note'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
