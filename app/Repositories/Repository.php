<?php

namespace App\Repositories;

use App\Tweet;
use App\Parsers\Processor;
use Illuminate\Database\Eloquent\Collection;

interface Repository
{
    public function insertJson(array $data, Processor $parser);
    public function update(Processor $parser, Tweet ...$tweets);
    public function delete(Tweet ...$tweets);
}
