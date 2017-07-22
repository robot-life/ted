<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Tweet;
use App\Salutation;
use App\User;
use App\Rating;

class MigrateSalutationsAndRatings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $jeff = User::where('email', 'jeff@jeffpuckett.com')->first();

        if (empty($jeff)) {
            $jeff = User::Create([
                'name' => 'Jeff Puckett',
                'email' => 'jeff@jeffpuckett.com',
                'password' => bcrypt('password'),
            ]);
        }

        $tweets = Tweet::whereNotNull('salutation')->get();

        $tweets->map(function (Tweet $tweet) use ($jeff) {
            $salutation = new Salutation([
                'name' => $tweet->salutation,
            ]);

            $tweet->salutations()->save($salutation);

            if (!empty($tweet->rating)) {
                $rating = new Rating([
                    'rate' => $tweet->rating,
                ]);

                $rating->user_id = $jeff->id;

                $salutation->ratings()->save($rating);
            }
        });
    }
}
