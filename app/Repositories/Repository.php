<?php

namespace App\Repositories;

use App\Tweet;
use Illuminate\Database\Eloquent\Collection;

interface Repository
{
    public function getNew(int $limit = 0) : Collection;
    public function patch(Tweet ...$tweets);
    public function delete(Tweet ...$tweets);
}
