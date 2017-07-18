<?php

namespace App;

use OauthPhirehose;
use DB;

class Streamer extends OauthPhirehose
{
    protected $statement;

    public function __construct(...$args)
    {
        parent::__construct(...$args);
        $this->setStatment();
    }

    public function setStatment(string $sql = null)
    {
        if (is_null($sql)) {
            $sql = 'INSERT INTO tweets (json) VALUES (?)';
        }

        $this->statement = DB::connection()->getPdo()->prepare($sql);
    }

    /**
     * Enqueue each status
     *
     * @param string $status
     */
    public function enqueueStatus($status)
    {
        $this->statement->execute([$status]);
    }
}
