<?php

namespace App\Repositories;

use App\Tweet;
use App\Parsers\Processor;
use Illuminate\Database\Eloquent\Collection;

interface Repository
{
    /**
     * @return array Returns count of rows inserted indexed by table name.
     */
    public function insertJson(array $data, Processor $parser) : array;
    public function update(Processor $parser, Tweet ...$tweets);
    public function delete(Tweet ...$tweets);
}
