<?php

namespace Tests\Feature\UseCase;

use App\Facades\PhoneVerificationFacade;
use App\Models\Categories\Category;
use App\Models\User;
use App\Models\User\Profile;
use App\Notifications\VerifyPhoneNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class BasicUseCaseTest extends TestCase
{
  use RefreshDatabase;

  /**
   * Method to test basic use-case
   *
   * @return void
  */
  public function testBasic() {
    // Fill database
    $this->fillDatabase();

    // Perform search by category
    $profile = $this->searchRandomProfile();

    // Get profile page
    $this->get(route('api.profiles.id', ['id' => $profile->id]))
      ->assertOk();

    // Register profile view
    $this->post(route('api.profiles.views.create', ['profile' => $profile->id]))
      ->assertOk();

    // Register opening phone profile
    $this->post(route('api.profiles.views.create', ['profile' => $profile->id]), ['opened' => true])
      ->assertOk();

    // Create account
    $token = $this->createAccount();

    // View profile again
    $this->post(route('api.profiles.views.create', ['profile' => $profile->id]), [
      'headers' => ["Authorization" => "Bearer $token"]
    ]);

    // Check views on profile
    $p = Profile::find($profile->id);
    $this->assertEquals(1, $p->views_count);
    $this->assertEquals(1, $p->open_count);

    // Clear database
    $this->clearDatabase();
    }

    /**
     * Method to get profile from search
     *
     * @return Profile
    */
    protected function searchRandomProfile(): Profile {
      $category = Category::query()->inRandomOrder()->first();

      $data = $this->get(route('api.profiles.search', ['category_id' => $category->id]))
        ->assertOk()
        ->json('result.data');

      $profileId = $data[0]['id'];

      return Profile::query()->find($profileId);
    }

    /**
     * Method to create account
     *
     * @return string
    */
    protected function createAccount(): string {
      $userForm = factory(User::class)->make();
      $form = $userForm->toArray();
      $phone = $userForm->phone;
      $form['email'] = $userForm->email;
//      $form['phone'] = $userForm->phone;
      $form['verification_uuid'] = $this->verifyPhone($phone);
      $form['password'] = Str::random();
      $form['password_confirmation'] = $form['password'];

      $response = $this->post(route('api.register'), $form)
        ->assertOk()
        ->json();

      $token = $this->post(route('api.login'), $form)
        ->assertOk()
        ->json('access_token');

      Auth::logout();

      return $token;
    }

    /**
     * Verify phone number
     *
     * @param string $phone
     *
     * @return string
    */
    protected function verifyPhone(string $phone): string {
      $uuid = $this->post(route('api.phones'), ['phone' => $phone])
        ->assertOk()
        ->json('verification_uuid');
      $code = Cache::get(PhoneVerificationFacade::getCacheKey($uuid))['code'];
      $this->post(route('api.verify', ['uuid' => $uuid]), ['code' => $code])
        ->assertOk();
      return $uuid;
    }
}
