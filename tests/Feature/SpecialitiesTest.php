<?php

namespace Tests\Feature;

use App\Models\Categories\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tests\TestCase;

class SpecialitiesTest extends TestCase
{
  use RefreshDatabase;

  /**
   * Basic test for testing endpoints
   *
   * @return void
   */
  public function testEndpoints()
  {
    // Fill database
    $this->fillDatabase();

    $form = $this->getCreationForm();
    // Try to create speciality without login
    $this->post(route('api.user.profile.specialities.create'), $form)
      ->assertStatus(401);

    $user = $this->createUser();
    Auth::login($user);
    // Try to create speciality without profile
    $this->post(route('api.user.profile.specialities.create'), $form)
      ->assertStatus(403);

    // Create profile
    $profile = $this->createProfile($user);
    $specialitiesAmount = $profile->specialities()->count();

    // Create speciality
    $this->sendPartialRequests(route('api.user.profile.specialities.create'), $form, $this->getRequiredFields());
    $speciality = $this->post(route('api.user.profile.specialities.create'), $form)
      ->assertOk()
      ->json('speciality');

    // Check the amount of specialities
    $this->checkAmount($specialitiesAmount + 1);

    // Update speciality
    $updateForm = $this->getUpdateForm();
    $this->put(route('api.user.profile.specialities.update', ['specialityId' => $speciality['id']]), $updateForm)
      ->assertOk(); return;

    // Check information on speciality
    $speciality = $user->profile()->first()->specialities()->first();
    $this->assertEquals($updateForm['name'], $speciality->name);
    $this->assertEquals($updateForm['price'], $speciality->price);

    // Delete speciality
    $this->delete(route('api.user.profile.specialities.delete', ['specialityId' => $speciality['id']]))
      ->assertOk();

    // Check amount of specialities
    $this->checkAmount($specialitiesAmount);

    // Clear database
    $this->clearDatabase();
  }

  /**
   * Method to check amount of specialities
   *
   * @param int $count
   */
  protected function checkAmount(int $count)
  {
    $specialities = $this->get(route('api.user.profile.specialities.list'))
      ->assertOk()
      ->json('specialities');

    $this->assertEquals($count, sizeof($specialities));
  }

  /**
   * Method to get form for creation
   *
   * @return array
   */
  protected function getCreationForm(): array
  {
    return [
      'name' => Str::random(8) . " " . Str::random(8),
      'price' => rand(100, 600) / 10.0,
      'category_id' => Category::query()->inRandomOrder()->first()->id,
    ];
  }

  /**
   * Method to get form for updating
   *
   * @return array
   */
  protected function getUpdateForm(): array
  {
    return [
      'name' => Str::random(8) . " " . Str::random(8),
      'price' => rand(100, 600) / 10.0,
    ];
  }

  /**
   * Method to get required fields for creation
   *
   * @return array
   */
  public function getRequiredFields(): array
  {
    return [
      'name',
      'price',
      'category_id',
    ];
  }
}
