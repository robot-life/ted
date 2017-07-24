<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Parsers\Processor;
use App\Repositories\Pdo;
use App\Parsers\Hydrators\Extractor;
use App\Parsers\Filters\Retweet;
use App\Parsers\Lexers\Regex;

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
        $repository = new Pdo;
        $parcy = new Processor;

        $parcy->addHydrators(new Extractor);
        $parcy->addFilters(new Retweet);
        $parcy->addLexers(new Regex);

        while (true) {
            $this->line('');
            $this->info('parsing');

            $tweets = $repository->getNew();
            $repository->process($parcy, ...$tweets);

            $sleep = 30;
            $bar = $this->output->createProgressBar($sleep);
            $this->info('sleeping');
            while ($sleep--) {
                $bar->advance();
                sleep(1);
            }
            $bar->finish();
            sleep(1);
        }
    }
}
