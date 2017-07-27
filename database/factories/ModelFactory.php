<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Tweet::class, function (Faker\Generator $faker) {
    $tweet = [
        'id' => $faker->unique()->randomNumber(),
        'text' => $faker->sentence(),
        'created_at' => (string) new Carbon\Carbon,
    ];

    $tweet['json'] = json_encode($tweet);

    return $tweet;
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Salutation::class, function (Faker\Generator $faker) {
    return [
        'tweet_id' => function () {
            return factory('App\Tweet')->create()->id;
        },
        'text' => $faker->sentence,
    ];
});
