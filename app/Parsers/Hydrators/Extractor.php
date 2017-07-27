<?php

namespace App\Parsers\Hydrators;

use App\Tweet;
use Carbon\Carbon;

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

    /**
     * @return bool Returns TRUE if tweet was hydrated.
     */
    public function hydrate(Tweet $tweet) : bool
    {
        if (!isset($tweet->json->id, $tweet->json->text, $tweet->json->created_at)) {
            return false;
        }

        $tweet->id = $tweet->json->id;
        $tweet->text = $tweet->json->extended_tweet->full_text ?? $tweet->json->text;
        $tweet->created_at = new Carbon($tweet->json->created_at);

        return true;
    }
}
