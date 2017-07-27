<?php

namespace App\Parsers\Lexers;

use App\Tweet;
use App\Salutation;

class Regex implements Lexer
{
    /**
     * @return array of substrings from statement
     */
    public function lex(Tweet $tweet) : array
    {
        $regex = '/(major|private|general|kernel) \w[\w\-]*/';

        $matches = [];

        preg_match($regex, $tweet->text, $matches);

        if (empty($matches[0])) {
            return [];
        }

        return [$matches[0]];
    }
}
