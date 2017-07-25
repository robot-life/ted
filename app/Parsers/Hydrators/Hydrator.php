<?php

namespace App\Parsers\Hydrators;

use App\Tweet;

interface Hydrator
{
    public function getAttributes() : array;

    /**
     * @return bool Returns TRUE if tweet was hydrated.
     */
    public function hydrate(Tweet $tweet) : bool;
}
