<?php

namespace App\Parsers\Hydrators;

use App\Tweet;

class Empath implements Hydrator
{
    public function getAttributes() : array
    {
        return [
            'sentiment',
        ];
    }

    /**
     * @return bool Returns TRUE if tweet was hydrated.
     */
    public function hydrate(Tweet $tweet) : bool
    {
        return false;
    }

    public function query(string $text)
    {

    }
}
