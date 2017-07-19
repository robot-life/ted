<?php

namespace App\Lexers;

use App\Tweet;

class Regex implements Lexer
{
    /**
     * @return mixed
     *   substring from input or FALSE on failure
     */
    public function lex(Tweet $tweet)
    {
        $json = $tweet->json;
        $statement = $json->extended_tweet->full_text ?? $json->text;
        $regex = '/(major|private|general|kernel) \w[\w\-]*/';
        $matches = [];

        if (false == preg_match($regex, $statement, $matches)) {
            return false;
        }

        return $matches[0];
    }
}
