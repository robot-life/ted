<?php

namespace App\Lexers;

class Regex implements Lexer
{
    /**
     * @return mixed
     *   substring from input or FALSE on failure
     */
    public function lex(string $statement)
    {
        $regex = '/(major|private|general|kernel) \w[\w\-]*/';
        $matches = [];

        if (false == preg_match($regex, $statement, $matches)) {
            return false;
        }

        return $matches[0];
    }
}
