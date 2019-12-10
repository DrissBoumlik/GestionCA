<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ProjectSkill;
use Faker\Generator as Faker;

$factory->define(ProjectSkill::class, function (Faker $faker) {
    return [
        'project_id' => $faker->numberBetween(1, 50),
        'skill_id' => $faker->numberBetween(1, 26),
    ];
});
