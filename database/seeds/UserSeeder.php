<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
  protected $users = [
    [
      'phone' => '751214215',
      'email' => 'example@gmail.com',
      'password' => 'password',
    ]
  ];

  public function __construct()
  {
    $this->users = array_map(function ($user) {
      $user['password'] = \Illuminate\Support\Facades\Hash::make($user['password']);
      return $user;
    }, $this->users);
  }

  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $regions = \App\Models\Location\Region::all()->load(['cities.districts']);
    foreach ($this->users as $user) {
      try {
        $this->createUser($regions, $user);
      } catch (\Exception $exception) {
        \Illuminate\Support\Facades\Log::error("Error on creating user - {$exception->getMessage()}");
      }
    }
    for ($i = 0; $i < 10; $i++) {
      $this->createUser($regions);
    }
  }

  /**
   * Method to create a single users
   *
   * @param \Illuminate\Support\Collection $regions
   * @param ?array $params
   *
   * @return \App\Models\User
   */
  protected function createUser(\Illuminate\Support\Collection $regions, ?array $params = null): \App\Models\User
  {
    $region = $regions->random();
    $city = $region->cities->random();
    /* @var \Illuminate\Support\Collection $districts */
    $districts = $city->districts;
    $district = $districts->isNotEmpty() ? $districts->random() : null;
    return factory(\App\Models\User::class)
      ->create(array_merge([
        'region_id' => $region->id,
        'city_id' => $city->id,
        'district_id' => $district ? $district->id : null,
      ], $params ?? []));
  }
}
