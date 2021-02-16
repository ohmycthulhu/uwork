<?php

namespace Tests\Feature;

use App\Helpers\PhoneVerificationHelper;
use App\Models\User;
use App\Notifications\VerifyPhoneNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserTest extends TestCase
{
  use RefreshDatabase;

  /**
   * Basic test to check basic functionality
   *
   * @return void
   */
  public function testUsers()
  {
    // Create users
    $users = factory(User::class, 5)->create();

    $this->assertDatabaseCount('users', 5);

    // Authenticate user
    Auth::login($users->random());

    $this->assertAuthenticated();

    // Delete user
    $users->each(function ($u) {
      $u->forceDelete();
    });
    $this->assertDatabaseCount('users', 0);
  }

  /**
   * Test registration
   *
   * @return void
   */
  public function testRegistration()
  {
    Notification::fake();

    $form = $this->userForm();

    for ($i = 0; $i < sizeof($form) - 1; $i++) {
      $this->post(route('api.register'), array_slice($form, 0, $i))
        ->assertStatus(403);
    }

    $uuid = $this->post(route('api.register'), $form)
      ->assertOk()
      ->json('verification_uuid');

    $this->assertNotNull($uuid);

    $user = User::first();

    Notification::assertSentTo($user, VerifyPhoneNotification::class,
      function ($notification) use ($user) {
        $data = $notification->toArray($user);

        $this->assertArrayHasKey('code', $data);
        $this->assertNotNull($data['code']);

        return true;
      });

    $cached = Cache::get(PhoneVerificationHelper::getCacheKey($uuid));

    $this->assertNotNull($cached);

    $code = $cached['code'];

    // Test code verification
    $this->post(route('api.verify', ['uuid' => $uuid]))
      ->assertStatus(403);
    $this->post(route('api.verify', ['uuid' => $uuid]), ['code' => $code])
      ->assertOk();
    $user = User::first();
    $this->assertEquals(true, $user->phone_verified);

    // Test code reset
    $form = $this->userForm();
    $this->post(route('api.register'), $form)
      ->assertOk();

    $phone = $form['phone'];

    for ($i = 0; $i < 3; $i++) {
      $this->post(route('api.resend', ['phone' => $phone]))
        ->assertOk();
    }
    $this->post(route('api.resend', ['phone' => $phone]))
      ->assertStatus(403);

    User::query()->forceDelete();
  }

  /**
   * Get user form
   *
   * @return array
  */
  protected function userForm() {
    $model = factory(User::class)->make();
    $password = Str::random();
    return array_merge($model->toArray(), [
      'phone' => $model->phone,
      'email' => $model->email,
      'password' => $password,
      'password_confirmation' => $password,
    ]);
  }
}
