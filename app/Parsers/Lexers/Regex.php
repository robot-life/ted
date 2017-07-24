<?php

namespace App\Parsers\Lexers;

use App\Tweet;
use App\Salutation;

class Regex implements Lexer
{
    /**
     * @return mixed
     *   substring from input or FALSE on failure
     */
    public function lex(Tweet $tweet)
    {
        $regex = '/(major|private|general|kernel) \w[\w\-]*/';
        $matches = [];

        if (false == preg_match($regex, $tweet->text, $matches)) {
            return false;
        }

        return $matches[0];
    }
}
