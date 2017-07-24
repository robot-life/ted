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
    protected $attributes = [
        'id',
        'text',
    ];

    public function getNew(int $limit = 0) : Collection
    {
        if ($limit <= 0) {
            $limit = config('repository.limit', '1000');
        }

        if ($limit <= 0) {
            throw new Exception('Repository limit must be greater than zero.');
        }

        $tweets = Tweet::withoutGlobalScopes()
            ->select([
                'dbid',
                'json',
            ])
            ->whereNull('id')
            ->limit($limit)
            ->get();

        $this->extract(...$tweets);

        return $tweets;
    }

    protected function extract(Tweet ...$tweets)
    {
        // all this complexity just so I can reuse a prepared statement
        $builder = DB::table((new Tweet)->getTable())->where('dbid', '');
        $sql = $builder->getGrammar()
            ->compileUpdate($builder, [
                'id' => null,
                'text' => null,
            ]);

        // prepare once
        $statement = DB::connection()->getPdo()->prepare($sql);

        foreach ($tweets as $tweet) {
            // just call json_decode one time
            $json = $tweet->json;

            $data = [
                $tweet->id = $json->id_str, // tweet_id
                $tweet->text = $json->extended_tweet->full_text ?? $json->text,
                $tweet->dbid, // local id, WHERE statement binders come last
            ];

            // execute many
            $statement->execute($data);
        }
    }

    public function process(Processor $processor, Tweet ...$tweets)
    {
        // all this complexity just so I can reuse a prepared statement
        $builder = DB::table((new Tweet)->getTable())->where('dbid', '');
        $sql = $builder->getGrammar()
            ->compileUpdate($builder, array_flip($processor->getHydratorAttributes()));
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
            $hydrated = $processor->hydrate($tweet)

            if ($processor->filter($tweet)) {
                $trash []= $tweet;
                continue;
            }

            $data = $processor->lex($tweet)
            if (false === $data) {
                $trash []= $tweet;
                continue;
            }

            // execute many
            if ($hydrated) {
                $updateTweet->execute(array_merge($data, [$tweet->dbid]));
            }
            foreach ($data as $values) {
                try {
                    $createSalutation->execute(array_merge($values, [$tweet->dbid]));
                }
                catch (PDOException $exception) {
                    // ignore duplicate entries
                    if ($exception->getCode() != '23000') {
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
            $ids []= $tweet->id;
        }

        if (empty($ids)) {
            return;
        }

        return Tweet::withoutGlobalScopes()
            ->whereIn('id', $ids)
            ->delete();
    }
}
