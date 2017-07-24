<?php

namespace App\Parsers\Hydrators;

use App\Tweet;

class Extractor implements Hydrator
{
    public function getAttributes() : array
    {
        return [
            'id',
            'text',
            'created_at',
        ];
    }

    public function hydrate(Tweet $tweet)
    {
        $tweet->id = $tweet->json->id;
        $tweet->text = $tweet->json->extended_tweet->full_text ?? $tweet->json->text;
        $tweet->created_at = $tweet->json->created_at,
    }
}
