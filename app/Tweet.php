<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tweet extends Model
{
    protected $fillable = [
        'rating',
    ];

    public function getJsonAttribute($value)
    {
        return json_decode($value);
    }
}
