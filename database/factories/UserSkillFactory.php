<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\UserSkill;
use Faker\Generator as Faker;

$factory->define(UserSkill::class, function (Faker $faker) {
    return [
        'user_id' => $faker->numberBetween(1, 101),
        'skill_id' => $faker->numberBetween(1, 26),
        'isTopSkill' => $faker->boolean
    ];
});
