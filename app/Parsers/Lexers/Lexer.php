<?php

namespace App\Parsers\Lexers;

use App\Tweet;

interface Lexer
{
    /**
    * @return array of substrings from statement
     */
    public function lex(Tweet $tweet) : array;
}
