<?php

namespace App\Parsers\Hydrators;

use App\Tweet;

interface Hydrator
{
    public function getAttributes() : array;
    public function hydrate(Tweet $tweet);
}
