<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\User\Profile::class, function (Faker $faker) {
  return [
    'about' => $faker->text,
    'phone' => $faker->phoneNumber,
    'phone_verified' => $faker->boolean,
  ];
});
