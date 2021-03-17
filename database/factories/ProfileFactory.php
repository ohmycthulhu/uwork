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

$factory->define(\App\Models\User\ProfileSpeciality::class, function (Faker $faker) {
  return [
    'price' => $faker->numberBetween(10, 100),
    'name' => $faker->words(3, true),
    'description' => $faker->realText(),
  ];
});
