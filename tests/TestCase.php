<?php

namespace Tests;

use App\Models\Categories\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

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
}
