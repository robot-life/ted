<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Salutation extends Model
{
    protected $fillable = [
        'name',
    ];

    public function tweet()
    {
        return $this->belongsTo(Tweet::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}
