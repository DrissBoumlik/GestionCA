<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ProjectUser;
use Faker\Generator as Faker;

$factory->define(ProjectUser::class, function (Faker $faker) {
    return [
        'project_id' => $faker->numberBetween(1, 50),
        'user_id' => $faker->numberBetween(1, 101),
    ];
});
