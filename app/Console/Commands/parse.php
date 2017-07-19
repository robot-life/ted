<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Parser;
use App\Repositories\MySqlDb;
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
        $parcy->setRepository(new MySqlDb);
        $parcy->addFilter(new Retweet);
        $parcy->addLexer(new Regex);
        $parcy->parse();
    }
}