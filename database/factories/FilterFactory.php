<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Filter;
use Faker\Generator as Faker;

$factory->define(Filter::class, function (Faker $faker) {
    return [
        'user_id' => $faker->numberBetween(1, 2),
        'route' => 'route' . $faker->randomNumber(),
        'date_filter' => '"[{date: 1}, {date: 2}]"'
    ];
});
