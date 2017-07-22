<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Salutation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'text',
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
