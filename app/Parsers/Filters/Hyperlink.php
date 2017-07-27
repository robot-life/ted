<?php

namespace App\Parsers\Filters;

use App\Tweet;

class Hyperlink implements Filter
{
    /**
     * @return mixed
     *   Returns TRUE if input is filtered.
     */
    public function filters(Tweet $tweet) : bool
    {
        $pattern = '/(major|private|general|kernel) http/';

        return 1 === preg_match($pattern, $tweet->text);
    }
}
