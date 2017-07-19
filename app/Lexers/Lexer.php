<?php

namespace App\Lexers;

interface Lexer
{
    /**
     * @return mixed
     *   substring from statement or FALSE on failure
     */
    public function lex(string $input);
}
