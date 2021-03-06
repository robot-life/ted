<?php

namespace App\Parsers\Filters;

use App\Tweet;

interface Filter
{
    /**
     * @return bool
     *   Returns TRUE if filter catches tweet, else FALSE.
     */
    public function filters(Tweet $tweet) : bool;
}
