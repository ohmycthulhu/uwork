<?php

namespace Tests\Feature;

use App\Models\Location\City;
use App\Models\User;
use App\Notifications\PasswordResetNotification;
use App\Notifications\VerifyPhoneNotification;
use App\Utils\CacheAccessor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\Types\Void_;
use Tests\TestCase;

class UserTest extends TestCase
{
  use RefreshDatabase;

  protected $storeVerifying;

  public function __construct(?string $name = null, array $data = [], $dataName = '')
  {
    parent::__construct($name, $data, $dataName);
    $this->storeVerifying = new CacheAccessor("phone-verifying");
  }

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
   * Test phone verification
   *
   * @return void
  */
  public function testVerification () {
    Notification::fake();

    $phone = "994512318822";

    // Send request without phone
    $this->post(route('api.phones'))
      ->assertStatus(403);

    // Send request to set phone
    $this->verifyNumber($phone);

    // Send request with existing phone
    $this->verifyNumber($phone);
  }

  /**
   * Test registration
   *
   * @return void
   */
  public function testRegistration()
  {
    $this->fillDatabase();
    Notification::fake();

    // Send request to set mobile phone
    $phone = "994512318822";

    $uuid = $this->verifyNumber($phone);
    $uuid2 = $this->verifyNumber($phone);

    // Send partial requests
    // Send request to register with the first code
    $form = $this->userForm($uuid);
    $this->sendPartialRequests(route('api.register'), $form, [
      'first_name', 'last_name', 'father_name', 'password', 'verification_uuid',
      'city_id', 'region_id',
    ]);
    $userId = $this->post(route('api.register'), $form)
      ->assertOk()
      ->json('user.id');

    // Verify if user was created
    $user = User::find($userId);
    $this->assertNotNull($user);
    $this->assertEquals($phone, User::find($userId)->phone);
    $this->assertNotNull($user->avatar);
    $this->assertNotNull($user->avatarUrl);

    // Repeat request but with another code
    $form = $this->userForm($uuid2);
    $this->post(route('api.register'), $form)
      ->assertStatus(403);

    User::find($userId)->forceDelete();
  }

  /**
   * Get user form
   *
   * @param string $uuid
   *
   * @return array
  */
  protected function userForm(string $uuid): array {
    $model = factory(User::class)->make();
    $password = Str::random();
    $city = City::query()->first();

    return array_merge($model->toArray(), [
      'verification_uuid' => $uuid,
      'password' => $password,
      'password_confirmation' => $password,
      'avatar' => $this->getUploadedFile(),
      'region_id' => $city->region_id,
      'city_id' => $city->id,
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
      ->create(['password' => Hash::make($password)]);

    $this->get(route('api.user.get'))
      ->assertStatus(401);

    $this->post(route('api.login'), ['email' => $user->email])
      ->assertStatus(403);

    $this->post(route('api.login'), ['phone' => $user->phone])
      ->assertStatus(403);

    $token = $this->post(route('api.login'), ['phone' => $user->phone, 'password' => $password])
      ->assertOk()
      ->json(['access_token']);

    $this->assertNotNull($token);

    $userId = $this->get(route('api.user.get'), ['headers' => ["Authorization" => "Bearer $token"]])
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
      ->create();

//    $user->verifyPhone();

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
      $code = $notification->toArray($user)['code'];

      $this->post(route('api.reset.verify'), compact('uuid', 'code'))
        ->assertOk();

      $this->post(route('api.reset.set', compact('uuid')), $form)
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

    $form = ['avatar' => $this->getUploadedFile()];
    $this->post(route('api.user.update.profile'), $form)
      ->assertOk();

    /* @var User $user */
    $user = User::query()->find($user->id);

    $this->assertEquals($name, $user->first_name);
    $this->assertNotNull($user->avatar_url);
    $this->assertFileExists(public_path($user->avatar_path));
    $this->assertGreaterThan(1e3, File::size(public_path($user->avatar_path)));

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
    $this->changeUserPhone($user);

    // Delete user
    Auth::logout();
    User::query()->forceDelete();
  }

  protected function changeUserPhone(User $user) {
    $newPhone = '99512461251';

    $form = ['phone' => $newPhone];
    $this->put(route('api.user.update.phone'), $form)
      ->assertStatus(403);

    $verUuid = $this->verifyNumber($user->getPhone());

    $form = ['phone' => $newPhone, 'verification_uuid' => $verUuid];
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

      return true;
    });
  }

  protected function verifyNumber(string $phone) {
    // Send request to set mobile phone
    $uuid = $this->post(route('api.phones'), compact('phone'))
      ->assertOk()
      ->json('verification_uuid');

    // Verify mobile phones
    $code = $this->storeVerifying->get($uuid)['code'];

    $this->post(route('api.verify', compact('uuid')), compact('code'))
      ->assertOk();

    return $uuid;
  }
}
