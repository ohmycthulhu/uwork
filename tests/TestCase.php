<?php

namespace Tests;

use App\Models\Categories\Category;
use App\Models\Location\City;
use App\Models\Location\District;
use App\Models\Location\Region;
use App\Models\User;
use App\Models\User\ProfileSpeciality;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Method to create user
     *
     * @return User
    */
    protected function createUser(): User {
      return factory(User::class)->create();
    }

    /**
     * Method to create profile
     *
     * @param User $user
     *
     * @return User\Profile
    */
    protected function createProfile(User $user): User\Profile {
      $profile = $user->profile()->create(factory(User\Profile::class)->make()->toArray());

      if (Category::query()->count() <= 0) {
        factory(Category::class, 10)->create();
      }
      $categories = Category::query()->inRandomOrder()->take(3)->pluck('id');

      foreach ($categories as $category) {
        $profile->specialities()->create(factory(User\ProfileSpeciality::class)->make([
          'category_id' => $category
        ])->toArray());
      }

      return $profile;
    }


  /**
   * Method to fill database
   *
   * @return void
   */
  protected function fillDatabase()
  {
    Notification::fake();
    $categories = $this->createCategories();
    $regions = $this->createRegions();

    for ($i = 0; $i < 10; $i++) {
      $user = $this->createUser();
      $profile = $this->createProfile($user);
      $profile->specialities()
        ->createMany(
          $categories->shuffle()
            ->take(3)
            ->map(function ($c) {
              return factory(ProfileSpeciality::class)
                ->make(['category_id' => $c['id']]);
            })
            ->toArray()
        );
      $region = $regions->random();
      $city = $region->cities->random();
      $district = rand() % 2 === 0 ? $city->districts->random() : null;

      $profile->region_id = $region->id;
      $profile->city_id = $city->id;
      $profile->district_id = $district ? $district->id : null;
      $profile->save();
    }
  }

  /**
   * Method to create categories
   *
   * @return Collection
   */
  protected function createCategories(): Collection
  {
    $categories = factory(Category::class, 4)->create();
    $categories->push(
      ...$categories->reduce(function ($acc, $c) {
      return array_merge(
        $acc,
        $c->children()
          ->createMany(factory(Category::class, 3)->make()->toArray())
          ->toArray()
      );
    }, [])
    );
    return $categories;
  }

  /**
   * Method to create regions
   *
   * @return Collection
   */
  protected function createRegions(): Collection
  {
    /* Fill regions */
    $regions = factory(Region::class, 4)
      ->create();
    foreach ($regions as $region) {
      $cities = $region->cities()
        ->createMany(
          factory(City::class, 5)
            ->make()
            ->toArray()
        );

      foreach ($cities as $city) {
        $districts = $city->districts()
          ->createMany(
            factory(District::class, 6)
              ->make()
              ->toArray()
          );

        $city->districts = $districts;
      }

      $region->cities = $cities;
    }

    return $regions;
  }

  /**
   * Method to clear database
   *
   * @return void
  */
  protected function clearDatabase() {
    User::query()->forceDelete();
    User\Profile::query()->forceDelete();
    Category::query()->forceDelete();
    Region::query()->forceDelete();
    City::query()->forceDelete();
    District::query()->forceDelete();
  }

  /**
   * Method to send the request step-by-step
   *
   * @param string $route
   * @param array $finalForm
   * @param array $requiredFields
  */
  protected function sendPartialRequests(string $route, array $finalForm, array $requiredFields) {
    for ($i = 0; $i < sizeof($finalForm); $i++) {
      $form = array_slice($finalForm, 0, $i);
      $keys = array_keys($form);
      // If there is not a single field in required, which is not presented in form, then break
      if (!array_filter($requiredFields, function ($field) use ($keys) {
        return !in_array($field, $keys);
      })) {
        break;
      }

      $this->post($route, $form)
        ->assertStatus(403);
    }
  }
}
