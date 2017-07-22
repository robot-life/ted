<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $fillable = [
        'rate',
        'user_id',
        'salutation_id',
    ];

    public function salutation()
    {
        return $this->belongsTo(Salutation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
