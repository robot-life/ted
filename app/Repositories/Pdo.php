<?php

namespace App\Repositories;

use DB;
use App\Tweet;
use Illuminate\Database\Eloquent\Collection;
use Exception;

class Pdo implements Repository
{
    public function getNew(int $limit = 0) : Collection
    {
        if ($limit <= 0) {
            $limit = config('repository.limit', '1000');
        }

        if ($limit <= 0) {
            throw new Exception('Repository limit must be greater than zero.');
        }

        return Tweet::withoutGlobalScopes()
            ->select([
                'id',
                'json',
            ])
            ->whereNull('tweet_id')
            ->limit($limit)
            ->get();
    }

    public function patch(Tweet ...$tweets)
    {
        // all this complexity just so I can reuse a prepared statement
        $builder = DB::table((new Tweet)->getTable())->where('id', '');
        $sql = $builder->getGrammar()
            ->compileUpdate($builder, [
                'tweet_id' => null,
                'tweet' => null,
                'salutation' => null,
            ]);

        // prepare once
        $statement = DB::connection()->getPdo()->prepare($sql);

        foreach ($tweets as $tweet) {
            // just call json_decode one time
            $json = $tweet->json;

            $data = [
                $json->id_str, // tweet_id
                $json->extended_tweet->full_text ?? $json->text,
                $tweet->salutation,
                $tweet->id, // database id, WHERE statement binders come last
            ];

            // execute many
            $statement->execute($data);
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
