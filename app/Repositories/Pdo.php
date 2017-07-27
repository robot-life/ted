<?php

namespace App\Repositories;

use DB;
use App\Tweet;
use App\Salutation;
use Illuminate\Database\Eloquent\Collection;
use Exception;
use App\Parsers\Processor;
use Redis;

class Pdo implements Repository
{
    /**
     * @return array Returns count of rows inserted indexed by table name.
     */
    public function insertJson(array $data, Processor $processor) : array
    {
        $tweetTable = (new Tweet)->getTable();
        $salutationTable = (new Salutation)->getTable();

        $count = [
            $tweetTable => 0,
            $salutationTable => 0,
        ];

        // all this complexity just so I can reuse a prepared statement
        $builder = DB::table($tweetTable);
        $columns = array_merge(['json'], $processor->getAttributes());
        $sql = $builder->getGrammar()
            ->compileInsert($builder, array_flip($columns));
        // prepare once
        $insertTweet = DB::connection()->getPdo()->prepare($sql);

        $builder = DB::table($salutationTable);
        $sql = $builder->getGrammar()
            ->compileInsert($builder, [
                'text' => null,
                'tweet_id' => null,
            ]);
        // prepare once
        $insertSalutation = DB::connection()->getPdo()->prepare($sql);

        foreach ($data as $json) {
            $tweet = new Tweet;
            $tweet->json = $json;

            // discover
            if (!$processor->hydrate($tweet)) {
                continue;
            }

            // filter
            if ($processor->filters($tweet)) {
                continue;
            }

            // reap
            $lexes = $processor->lex($tweet);
            if (!$lexes) {
                continue;
            }

            // execute many
            $attributes = [];
            foreach ($processor->getAttributes() as $attribute) {
                $attributes []= $tweet->$attribute;
            }
            $insertTweet->execute(array_merge([$json], $attributes));
            $count[$tweetTable]++;

            foreach ($lexes as $text) {
                try {
                    $insertSalutation->execute([$text, $tweet->id]);
                    $count[$salutationTable]++;
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

        return $count;
    }

    public function update(Processor $processor, Tweet ...$tweets)
    {
        // all this complexity just so I can reuse a prepared statement
        $builder = DB::table((new Tweet)->getTable())->where('id', '');
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
        $insertSalutation = DB::connection()->getPdo()->prepare($sql);

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
            if (!$data) {
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

            foreach ($data as $value) {
                if (empty($value)) {
                    dd($data);
                }
                try {
                    $insertSalutation->execute([$value, $tweet->id]);
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
            $ids []= $tweet->id;
        }

        if (empty($ids)) {
            return;
        }

        return Tweet::whereIn('id', $ids)->delete();
    }
}
