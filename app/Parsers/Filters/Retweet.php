<?php

namespace App\Parsers\Filters;

use App\Tweet;

class Retweet implements Filter
{
    /**
     * @return mixed
     *   Returns TRUE if input is filtered.
     */
    public function filters(Tweet $tweet) : bool
    {
        return isset($tweet->json->retweeted_status);
    }
}
