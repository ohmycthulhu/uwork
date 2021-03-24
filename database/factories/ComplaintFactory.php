<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\Complaints\Complaint::class, function (Faker $faker) {
  return [
    'ip_addr' => $faker->ipv4,
    'reason_other' => $faker->boolean ? $faker->text : null,
    'text' => $faker->text,
  ];
});

$factory->define(\App\Models\Complaints\ComplaintType::class, function (Faker $faker) {
  return [
    'name' => $faker->words(3, true)
  ];
});
