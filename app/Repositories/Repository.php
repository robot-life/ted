<?php

namespace App\Repositories;

use App\Tweet;
use Illuminate\Database\Eloquent\Collection;

interface Repository
{
    public function getNew(int $limit = 0) : Collection;
    public function process(Parser $parser, Tweet ...$tweets);
    public function delete(Tweet ...$tweets);
}
