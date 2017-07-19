<?php

namespace App\Repositories;

use DB;
use App\Tweet;
use Illuminate\Database\Eloquent\Collection;
use Exception;

class MySqlDb implements Repository
{
    protected $pdo;

    public function __construct()
    {
        $this->pdo = DB::connection()->getPdo();
    }

    public function getNew(int $limit = 0) : Collection
    {
        if ($limit <= 0) {
            $limit = config('repository.limit', '1000');
        }

        if ($limit <= 0) {
            throw new Exception('Repository limit must be greater than zero.');
        }

        return Tweet::select([
                'id',
                'json',
            ])
            ->whereNull('tweet_id')
            ->limit($limit)
            ->get();
    }

    public function patch(Tweet ...$tweets)
    {
        $sql = "UPDATE tweets
            SET tweet_id = :tweet_id
                ,tweet = :tweet
                ,salutation = :salutation
            WHERE id = :id
        ";

        $statement = $this->pdo->prepare($sql);

        foreach ($tweets as $tweet) {
            $json = $tweet->json;
            $data = [
                'id' => $tweet->id,
                'tweet_id' => $json->id_str,
                'tweet' => $json->text,
                'salutation' => $tweet->salutation,
            ];

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

        return Tweet::whereIn('id', $ids)->delete();
    }
}
