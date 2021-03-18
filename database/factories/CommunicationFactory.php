<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Communication\Appeal;
use App\Models\Communication\AppealReason;
use Faker\Generator as Faker;

$factory->define(AppealReason::class, function (Faker $faker) {
    return [
        'name' => $faker->words(2, true)
    ];
});

$factory->define(Appeal::class, function (Faker $faker) {
  return [
    'text' => $faker->realText(),
    'appeal_reason_other' => $faker->boolean ? $faker->realText() : null,
    'ip' => $faker->ipv4,
    'email' => $faker->email,
    'phone' => $faker->phoneNumber,
    'name' => $faker->name,
  ];
});
