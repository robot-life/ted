<?php

namespace App;

use App\Parsers\Parser;
use App\Tweet;

class Processor implements Parser
{
    protected $parsers = [];

    public function addParsers(Parser ...$parser)
    {

    }

    public function attributes() : array
    {
        $attributes = [];

        foreach ($this->parsers as $parser) {
            $attributes = array_merge_recursive($attributes, $parser->attributes());
        }

        return $attributes;
    }

    public function parse(Tweet $tweet)
    {
        if ($this->filter($tweet)) {
            return false;
        }

        foreach ($this->lex($tweet) as $result) {
            $return = array_merge_recursive($return ?? [], $result);
        }

        return $return ?? false;
    }

    protected function filter(Tweet $tweet) : bool
    {
        foreach ($this->filters as $filter) {
            if ($filter->catches($tweet)) {
                return true;
            }
        }

        return false;
    }

    protected function lex(Tweet $tweet) : int
    {
        $count = 0;

        foreach ($this->lexers as $lexer) {
            if (false !== ($response = $lexer->lex($tweet))) {
                $tweet->salutations()->save(new Salutation([
                    'text' => $response,
                ]));
                $count++;
            }
        }

        return $count;
    }
}
