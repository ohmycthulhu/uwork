<?php

namespace Tests\Feature;

use App\Models\Categories\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ProfileTest extends TestCase
{
  use RefreshDatabase;
  /**
   * Method to test database
   *
   * @return void
  */
  public function testDatabase() {
    // Create user
    $user = factory(User::class)->create();

    // Create categories
    $categories = factory(Category::class, 5)->create();

    // Create profile
    $profile = $user->profile()->create(
      factory(User\Profile::class)->make()->toArray()
    );

    $this->assertDatabaseCount('profiles', 1);

    // Attach profile specialities
    foreach ($categories as $category) {
      $profile->addSpeciality($category->id, rand(10, 100) / 10.0);
    }

    $this->assertDatabaseCount('profile_specialities', $categories->count());

    $category = $categories->random();
    $profile->removeSpeciality($category->id);

    $this->assertDatabaseCount('profile_specialities', $categories->count() - 1);

    // Delete user
    $user->forceDelete();

    // Delete categories
    $categories->each(function ($c) { $c->forceDelete(); });

    $this->assertDatabaseCount('profiles', 0);
  }

  /**
   * Test profile creation
   *
   * @return void
  */
  public function testCreation() {
    // Create user
    $user = factory(User::class)->create();

    // Create categories
    $categories = factory(Category::class, 10)->create();

    Auth::login($user);

    // Create right request form
    $form = $this->getCreationForm();

    // Send several malformed requests
    for ($i = 1; $i <= sizeof($form) - 1; $i++) {
      $this->post(route('api.profile.create'), array_slice($form, 0, $i))
        ->assertStatus(403);
    }

    // Send request and ensure everything is okay
    $this->post(route('api.profile.create'), $form)
      ->assertOk();

    $this->assertDatabaseCount('profiles', 1);

    // Delete user
    $user->forceDelete();

    // Delete categories
    $categories->each(function ($c) { $c->forceDelete(); });
  }

  /**
   * Get creation form
   *
   * @return array
  */
  protected function getCreationForm(): array {
    $categories = Category::query()->inRandomOrder()->take(3)->pluck('id');
    return [
      'about' => 'Some text about me',
      'specialities' => $categories->map(function ($id) {
        return ['category_id' => $id, 'price' => rand(100, 200) / 10];
      })->toArray(),
    ];
  }
}
