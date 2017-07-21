<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Parser;
use App\Repositories\Pdo;
use App\Filters\Retweet;
use App\Lexers\Regex;

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
        $parcy = new Parser;
        $parcy->setRepository(new Pdo);
        $parcy->addFilter(new Retweet);
        $parcy->addLexer(new Regex);

        while (true) {
            $this->line('');
            $this->info('parsing');
            $parcy->parse();

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
