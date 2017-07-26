<?php

namespace App\Parsers;

use App\Parsers\Hydrators\Hydrator;
use App\Parsers\Filters\Filter;
use App\Parsers\Lexers\Lexer;
use App\Tweet;

class Processor implements Hydrator, Filter, Lexer
{
    protected $hydrators = [];
    protected $attributes;
    protected $filters = [];
    protected $lexers = [];

    public function addHydrators(Hydrator ...$hydrators)
    {
        // clear cache
        $this->attributes = null;

        $this->hydrators = array_merge($this->hydrators, $hydrators);
    }

    public function addFilters(Filter ...$filters)
    {
        $this->filters = array_merge($this->filters, $filters);
    }

    public function addLexers(Lexer ...$lexers)
    {
        $this->lexers = array_merge($this->lexers, $lexers);
    }

    public function getAttributes() : array
    {
        // cache
        if (isset($this->attributes)) {
            return $this->attributes;
        }

        $attributes = [];

        foreach ($this->hydrators as $hydrator) {
            $attributes = array_merge_recursive($attributes, $hydrator->getAttributes());
        }

        return $this->attributes = $attributes;
    }

    /**
     * @return bool Returns TRUE if tweet was hydrated.
     */
    public function hydrate(Tweet $tweet) : bool
    {
        $hydrated = false;

        foreach ($this->hydrators as $hydrator) {
            if ($hydrator->hydrate($tweet)) {
                $hydrated = true;
            }
        }

        return $hydrated;
    }

    public function filters(Tweet $tweet) : bool
    {
        foreach ($this->filters as $filter) {
            if ($filter->filters($tweet)) {
                return true;
            }
        }

        return false;
    }

    public function lex(Tweet $tweet)
    {
        foreach ($this->lexers as $lexer) {
            if (false !== ($response = $lexer->lex($tweet))) {
                $results []= $response;
            }
        }

        return $results ?? false;
    }
}
