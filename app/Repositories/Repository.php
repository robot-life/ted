<?php

namespace App\Repositories;

use App\Tweet;
use App\Parsers\Processor;
use Illuminate\Database\Eloquent\Collection;

interface Repository
{
    public function getNew(int $limit = 0) : Collection;
    public function process(Processor $parser, Tweet ...$tweets);
    public function delete(Tweet ...$tweets);
}
