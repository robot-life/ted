<?php

namespace App\Lexers;

use App\Tweet;

interface Lexer
{
    /**
     * @return mixed
     *   substring from statement or FALSE on failure
     */
    public function lex(Tweet $tweet);
}
