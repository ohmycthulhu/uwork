<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\Categories\Category::class, function (Faker $faker) {
    return [
        'name' => $faker->words(3, true)
    ];
});
