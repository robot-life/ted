<?php

namespace App;

class Parser
{
    /**
     * @return mixed
     *   substring salutation from statement or FALSE on failure
     */
    public function parse(string $statement)
    {
        $regex = '/(major|private|general|kernel) \w[\w\-]*/';
        $matches = [];

        if (false == preg_match($regex, $statement, $matches)) {
            return false;
        }

        return $matches[0];
    }
}
