<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\Messenger\Chat::class, function (Faker $faker) {
  return [
    //
  ];
});

$factory->define(\App\Models\Messenger\Message::class, function (Faker $faker) {
  return [
    'text' => $faker->text,
    'attachment' => $faker->boolean ? $faker->imageUrl() : null,
  ];
});
