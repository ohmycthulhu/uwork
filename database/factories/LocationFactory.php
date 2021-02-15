<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\Location\Region::class, function (Faker $faker) {
    return [
      'name' => $faker->words(2, true),
    ];
});

$factory->define(\App\Models\Location\City::class, function (Faker $faker) {
    return [
        'name' => $faker->words(2, true),
    ];
});

$factory->define(\App\Models\Location\District::class, function (Faker $faker) {
    return [
        //
      'name' => $faker->words(2, true)
    ];
});
