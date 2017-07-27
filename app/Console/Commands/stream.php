<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Streamer;
use Phirehose;

class stream extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stream';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gets new tweets from Streaming API';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $sc = new Streamer(config('streamer.oauth_token'), config('streamer.oauth_secret'), Phirehose::METHOD_FILTER);
        $sc->consumerKey = config('streamer.consumer_key');
        $sc->consumerSecret = config('streamer.consumer_secret');
        $sc->setTrack(config('streamer.track'));
        $sc->setLang(config('streamer.language'));

        $sc->setLogger(new class($this) {
            protected $command;

            public function __construct(Command $command)
            {
                $this->command = $command;
            }

            public function log($msg)
            {
                $this->command->info($msg);
            }
        });

        $sc->consume();
    }
}
