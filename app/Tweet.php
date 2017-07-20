<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('newest', function (Builder $builder) {
            $builder->orderBy('id', 'desc');
        });
    }
}
