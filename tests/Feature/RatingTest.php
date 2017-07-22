<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Salutation;

class RatingTest extends TestCase
{
    use DatabaseMigrations;

    public function test_rates_salutation()
    {
        $salutation = create(Salutation::class);
        $data = [
            'rate' => 8,
        ];

        $this
            ->signIn()
            ->get("/salutations/{$salutation->id}")
            ->assertJsonMissing($data)
        ;

        $this
            ->post("/ratings", array_merge($data, [
                'salutation_id' => $salutation->id,
            ]))
            ->assertStatus(204)
        ;

        $this
            ->get("/salutations/{$salutation->id}")
            ->assertJson([
                'ratings' => [
                    $data,
                ],
            ])
        ;
    }
}
