<?php

namespace Tests\Feature;

use App\Facades\PhoneVerificationFacade;
use App\Helpers\PhoneVerificationHelper;
use App\Models\User;
use App\Notifications\PasswordResetNotification;
use App\Notifications\VerifyPhoneNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
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

    $cached = Cache::get(PhoneVerificationFacade::getCacheKey($uuid));

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

  /**
   * Method to test login
   *
   * @return void
  */
  public function testLogin() {
    $password = Str::random();
    $user = factory(User::class)
      ->create(['phone_verified' => false, 'password' => Hash::make($password)]);

    $this->get(route('api.user'))
      ->assertStatus(401);

    $this->post(route('api.login'), ['email' => $user->email, 'password' => $password])
      ->assertStatus(403);

    $this->post(route('api.login'), ['email' => $user->email])
      ->assertStatus(403);

    $this->post(route('api.login'), ['phone' => $user->phone])
      ->assertStatus(403);

    $user->verifyPhone();

    $token = $this->post(route('api.login'), ['phone' => $user->phone, 'password' => $password])
      ->assertOk()
      ->json(['access_token']);

    $this->assertNotNull($token);

    $userId = $this->get(route('api.user'), ['headers' => ["Authorization" => "Bearer $token"]])
      ->assertOk()
      ->json("user.id");

    $this->assertEquals($user->id, $userId);

    Auth::logout();
    User::query()->forceDelete();
  }

  /**
   * Method to test password reset
   *
   * @return void
  */
  public function testPasswordReset() {
    Notification::fake();
    // Create user
    $user = factory(User::class)
      ->create(['phone_verified' => false]);

    // Send request to reset non-existing user
    $this->post(route('api.reset'), ['phone' => $user->phone])
      ->assertStatus(403);

    $user->verifyPhone();

    // Send request to reset password
    $this->post(route('api.reset'), ['phone' => $user->phone])
      ->assertOk();

    $password = Str::random();
    $form = ['password' => $password, 'password_confirmation' => $password];

    // Send wrong token
    $randomUuid = Str::uuid();
    $this->post(route('api.reset.set', ['uuid' => $randomUuid]), $form)
      ->assertStatus(403);

    // Check if notification has been sent
    Notification::assertSentTo($user, PasswordResetNotification::class, function ($notification) use ($user, $form) {
      // Get token and set new password
      $uuid = $notification->toArray($user)['uuid'];

      $this->post(route('api.reset.set', ['uuid' => $uuid]), $form)
        ->assertOk();

      return true;
    });

    $this->post(route('api.login'), ['phone' => $user->phone, 'password' => $password])
      ->assertOk();

    Auth::logout();

    // Delete user
    $user->delete();
  }

  /**
   * Method to test profile change
   *
   * @return void
  */
  public function testUserChange() {
    Notification::fake();
    $password = Str::random();
    // Create user
    $user = factory(User::class)->create(['password' => Hash::make($password)]);

    // Authenticate user
    Auth::login($user);

    $name = Str::random();
    $form = ['first_name' => $name];
    // Send request to change name
    $this->put(route('api.user.update.profile'), $form)
      ->assertOk();

    $user = User::query()->find($user->id);

    $this->assertEquals($name, $user->first_name);

    $newPassword = Str::random();
    $form = ['current_password' => $newPassword, 'password' => $newPassword, 'password_confirmation' => $newPassword];
    // Send request to change password
    $this->put(route('api.user.update.password'), $form)
      ->assertStatus(403);

    $form['current_password'] = $password;
    $this->put(route('api.user.update.password'), $form)
      ->assertOk();

    $this->post(route('api.login'), ['phone' =>$user->phone, 'password' => $newPassword])
      ->assertOk();
    $password = $newPassword;

    // Send request to change email
    $form = ['email' => 'example@email.com', 'password' => $password];
    $this->put(route('api.user.update.email'), $form)
      ->assertOk();

    $this->assertEquals('example@email.com', User::query()->find($user->id)->email);

    // Send request to change phone
    $newPhone = '995124612';
    $form = ['phone' => $newPhone, 'password' => $password];
    $verificationUuid = $this->put(route('api.user.update.phone'), $form)
      ->assertOk()
      ->json('verification_uuid');

    // Check if phone has changed
    $this->assertEquals($user->phone, User::query()->find($user->id)->phone);

    // Ensure notification has been sent
    Notification::assertSentTo($user, VerifyPhoneNotification::class, function ($notification) use ($user, $verificationUuid, $newPhone) {
      $code = $notification->toArray($user)['code'];

      // Confirm phone and check if user's phone has changed
      $this->post(route('api.verify', ['uuid' => $verificationUuid]), ['code' => $code])
        ->assertOk();

      $u = User::query()->find($user->id);
      $this->assertEquals($newPhone, $u->phone);
      $this->assertTrue(!!$u->phone_verified);

      return true;
    });


    // Delete user
    Auth::logout();
    User::query()->forceDelete();
  }
}
