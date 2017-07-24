<?php

namespace App\Parsers\Lexers;

use App\Parsers\Parser;
use App\Tweet;
use App\Salutation;

class Regex implements Parser, Lexer
{
    public function attributes() : array
    {
        return [
            Salutation::class => [
                'text',
            ],
        ];
    }

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
