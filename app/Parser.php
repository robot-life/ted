<?php

namespace App;

use App\Repositories\Repository;
use App\Filters\Filter;
use App\Lexers\Lexer;
use App\Tweet;

class Parser
{
    protected $repository;
    protected $filters;
    protected $lexers;

    public function setRepository(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function addFilter(Filter $filter)
    {
        $this->filters []= $filter;
    }

    public function addLexer(Lexer $lexer)
    {
        $this->lexers []= $lexer;
    }

    public function parse()
    {
        $tweets = $this->repository->getNew();
        $trash = [];

        foreach ($tweets as $index => $tweet) {
            if ($this->filter($tweet)) {
                $trash []= $tweet;
                $tweets->forget($index);
                continue;
            }

            if (!$this->lex($tweet)) {
                $trash []= $tweet;
                $tweets->forget($index);
            }
        }

        $this->repository->delete(...$trash);
        $this->repository->patch(...$tweets);
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

    protected function lex(Tweet $tweet) : bool
    {
        foreach ($this->lexers as $lexer) {
            if (false !== ($response = $lexer->lex($tweet))) {
                $tweet->salutation = $response;
                return true;
            }
        }

        return false;
    }
}
