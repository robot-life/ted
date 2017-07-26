<?php

namespace App\Repositories;

use DB;
use App\Tweet;
use App\Salutation;
use Illuminate\Database\Eloquent\Collection;
use Exception;
use App\Parsers\Processor;

class Pdo implements Repository
{
    public function getNew(int $limit = 0) : Collection
    {
        if ($limit === 0) {
            $limit = (int)config('repository.limit', '1000');
        }

        if ($limit <= 0) {
            throw new Exception('Repository limit must be greater than zero.');
        }

        return Tweet::withoutGlobalScopes()
            ->select([
                'dbid',
                'json',
            ])
            ->whereNull('id')
            ->limit($limit)
            ->get();
    }

    public function process(Processor $processor, Tweet ...$tweets)
    {
        // all this complexity just so I can reuse a prepared statement
        $builder = DB::table((new Tweet)->getTable())->where('dbid', '');
        $sql = $builder->getGrammar()
            ->compileUpdate($builder, array_flip($processor->getAttributes()));
        // prepare once
        $updateTweet = DB::connection()->getPdo()->prepare($sql);

        $builder = DB::table((new Salutation)->getTable());
        $sql = $builder->getGrammar()
            ->compileInsert($builder, [
                'text' => null,
                'tweet_id' => null,
            ]);
        // prepare once
        $createSalutation = DB::connection()->getPdo()->prepare($sql);

        $trash = [];
        foreach ($tweets as $tweet) {
            // discover
            $hydrated = $processor->hydrate($tweet);

            // filter
            if ($processor->filters($tweet)) {
                $trash []= $tweet;
                continue;
            }

            // reap
            $data = $processor->lex($tweet);
            if (false === $data) {
                $trash []= $tweet;
                continue;
            }

            // execute many
            if ($hydrated) {
                $attributes = [];
                foreach ($processor->getAttributes() as $attribute) {
                    $attributes []= $tweet->$attribute;
                }
                $attributes []= $tweet->dbid;
                $updateTweet->execute($attributes);
            }

            foreach ($data as $values) {
                try {
                    $createSalutation->execute(array_merge([$values], [$tweet->id]));
                }
                catch (\PDOException $exception) {
                    if (!isset($exception->errorInfo[1])) {
                        throw $exception;
                    }

                    // ignore duplicate entries
                    // TODO: fix abstraction - this is MySQL specific :(
                    if ($exception->errorInfo[1] != 1062) {
                        throw $exception;
                    }
                }
            }
        }

        if ($trash) {
            $this->delete(...$trash);
        }
    }

    public function delete(Tweet ...$tweets)
    {
        foreach ($tweets as $tweet) {
            $ids []= $tweet->dbid;
        }

        if (empty($ids)) {
            return;
        }

        return Tweet::withoutGlobalScopes()
            ->whereIn('dbid', $ids)
            ->delete();
    }
}
