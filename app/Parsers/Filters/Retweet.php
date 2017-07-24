<?php

namespace App\Parsers\Filters;

use App\Parsers\Parser;
use App\Tweet;

class Retweet implements Parser, Filter
{
    public function attributes() : array
    {
        return [];
    }
    /**
     * @return mixed
     *   Returns TRUE if input is filtered.
     */
    public function catches(Tweet $tweet) : bool
    {
        return isset($tweet->json->retweeted_status);
    }
}
