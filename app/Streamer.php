<?php

namespace App;

use OauthPhirehose;
use Redis;

class Streamer extends OauthPhirehose
{
    /**
     * Enqueue each status
     *
     * @param string $status
     */
    public function enqueueStatus($status)
    {
        Redis::lpush('tweets', $status);
    }
}
