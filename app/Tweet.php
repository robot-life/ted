<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tweet extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'rating',
    ];

    public function getJsonAttribute($value)
    {
        return json_decode($value);
    }
}
