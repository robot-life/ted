<?php

namespace App;

use OauthPhirehose;
use Redis;

class Streamer extends OauthPhirehose
{
    protected $logger;

    /**
     * Enqueue each status
     *
     * @param string $status
     */
    public function enqueueStatus($status)
    {
        Redis::lpush('tweets', $status);
    }

    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    protected function log($message, $level = 'notice')
    {
        if (empty($this->logger)) {
            return;
        }

        $this->logger->log($message, $level);
    }
}
