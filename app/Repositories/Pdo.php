<?php

namespace App\Repositories;

use DB;
use App\Tweet;
use Illuminate\Database\Eloquent\Collection;
use Exception;

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

    public function process(Parser $parser, Tweet ...$tweets)
    {
        $trash = [];

        foreach ($parser->attributes() as $table => $columns) {
            // all this complexity just so I can reuse a prepared statement
            $object = new $table;
            $id = $object->primaryKey ?? 'id';
            if ($object instanceof Tweet) {
                $id = 'dbid';
            }

            $builder = DB::table($object->getTable())->where($id, '');
            $sql = $builder->getGrammar()
                ->compileUpdate($builder, array_flip($columns));

            // prepare once
            $$table = DB::connection()->getPdo()->prepare($sql);
        }

        foreach ($tweets as $tweet) {
            $data = $parser->parse($tweet)

            if (false === $data) {
                $trash []= $tweet;
                continue;
            }

            // execute many
            foreach ($data as $table => $values) {
                $$table->execute($values);
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
