<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Tweet;

class RatingTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_gets_welcome_page()
    {
        $this->get('/')->assertStatus(200);
    }

    public function test_rates_tweet()
    {
        $tweet = create(Tweet::class);
        $data = [
            'rating' => 8,
        ];

        $this
            ->get("/tweets/{$tweet->id}")
            ->assertJsonMissing($data)
        ;

        $this
            ->patch("/tweets/{$tweet->id}", $data)
            ->assertStatus(204)
        ;

        $this
            ->get("/tweets/{$tweet->id}")
            ->assertJson($data)
        ;
    }
}
