<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Parsers\Processor;
use App\Repositories\Pdo;
use App\Parsers\Hydrators\Extractor;
use App\Parsers\Filters\Retweet;
use App\Parsers\Lexers\Regex;
use Redis;

class parse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parses raw JSON tweets';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $redisList = 'tweets';
        $limit = (int)config('repository.limit', 5000);

        $repository = new Pdo;
        $parcy = new Processor;

        $parcy->addHydrators(new Extractor);
        $parcy->addFilters(new Retweet);
        $parcy->addLexers(new Regex);

        while (true) {
            Redis::multi();
            Redis::llen($redisList);
            Redis::lrange($redisList, 0, $limit);
            Redis::del($redisList);
            $result = Redis::exec();

            $this->info(date('c')." parsing $result[0] tweets");
            $count = $repository->insertJson($result[1], $parcy);
            $this->table(array_keys($count), [$count]);

            if ($result[0] < 500) {
                $this->sleep();
                continue;
            }
        }
    }

    protected function sleep(int $sleep = 60)
    {
        if ($sleep < 1) {
            return;
        }

        $this->info(date('c')." sleeping $sleep seconds");

        sleep($sleep);
    }
}
