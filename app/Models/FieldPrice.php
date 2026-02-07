<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldPrice extends Model
{
    protected $fillable = ['field_id', 'from_time', 'to_time', 'price', 'label'];

    public function field()
    {
        return $this->belongsTo(Field::class);
    }
}