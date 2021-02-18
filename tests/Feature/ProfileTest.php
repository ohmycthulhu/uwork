<?php

namespace Tests\Feature;

use App\Models\Categories\Category;
use App\Models\User;
use App\Notifications\VerifyPhoneNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
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
    Notification::fake();

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
    $response = $this->post(route('api.profile.create'), $form)
      ->assertOk();

    $profile = $response->json('profile');
    $verificationUuid = $response->json('verification_uuid');

    $this->assertDatabaseCount('profiles', 1);

    // Assert notification sent
    Notification::assertSentTo($user, VerifyPhoneNotification::class, function ($n) use ($user, $verificationUuid) {
      $code = $n->toArray($user)['code'];

      $this->post(route('api.verify', ['uuid' => $verificationUuid]), ['code' => $code])
        ->assertOk();

      return true;
    });

    // Check profile phone and phone verification
    $p = User\Profile::query()->find($profile['id']);
    $this->assertEquals($form['phone'], $p->phone);
    $this->assertTrue(!!$p->phone_verified);
    $this->assertEquals(1, $p->media()->count());
    if ($p->picture) {
      $this->assertFileExists(storage_path("app/public/{$p->picture}"));
    }

    // Delete user
    $user->forceDelete();

    // Delete categories
    $categories->each(function ($c) { $c->forceDelete(); });
  }

  /**
   * Method to test updating profiles
   *
   * @return void
  */
  public function testUpdating() {
    Notification::fake();

    // Create user and categories
    $user = factory(User::class)->create();
    factory(Category::class, 10)->create();

    Auth::login($user);

    // Get update form
    $form = $this->getUpdateForm();

    // Try updating profile
    $this->post(route('api.profile.update'), $form)
      ->assertStatus(403);

    // Create profile
    $user->profile()->create(factory(User\Profile::class)->make()->toArray());

    // Try updating profile
    $verificationUuid = $this->post(route('api.profile.update'), $form)
      ->assertOk()
      ->json('verification_uuid');

    // Check if profile was updated
    $profile = $user->profile()->first();
    $this->assertEquals($form['about'], $profile->about);
    $this->assertEquals(sizeof($form['images']), $profile->media()->count());
    Notification::assertSentTo($user, VerifyPhoneNotification::class, function ($n) use ($user, $verificationUuid) {
      $code = $n->toArray($user)['code'];

      $this->post(route('api.verify', ['uuid' => $verificationUuid]), ['code' => $code])
        ->assertOk();

      return true;
    });


    // Delete user and categories
    $user->forceDelete();
    Category::query()->forceDelete();
  }

  /**
   * Get creation form
   *
   * @return array
  */
  protected function getCreationForm(): array {
    $categories = Category::query()->inRandomOrder()->take(3)->pluck('id');
    $images = [$this->uploadImage()];
    return [
      'about' => 'Some text about me',
      'phone' => '1231251',
      'images' => $images,
      'avatar' => $this->getUploadedFile(),
      'specialities' => $categories->map(function ($id) {
        return ['category_id' => $id, 'price' => rand(100, 200) / 10];
      })->toArray(),
    ];
  }

  /**
   * Method to generate update form
   *
   * @return array
  */
  protected function getUpdateForm(): array {
    $categoriesToRemove = Category::query()->inRandomOrder()->take(3)->pluck('id');
    $categoriesToAdd = Category::query()->inRandomOrder()->take(3)->pluck('id');
    $images = [$this->uploadImage()];
    return [
      'about' => 'Another text',
      'phone' => '73512',
      'images' => $images,
      'avatar' => $this->getUploadedFile(),
      'remove_specialities' => $categoriesToRemove->toArray(),
      'add_specialities' => $categoriesToAdd->map(function ($id) {
        return ['category_id' => $id, 'price' => rand(100, 200) / 10];
      })->toArray(),
    ];
  }

  /**
   * Method to get uploaded file
   *
   * @return UploadedFile
  */
  protected function getUploadedFile(): UploadedFile {
    $name = Str::random().".jpg";

    return new UploadedFile(storage_path('test/image.jpg'), $name, 'image/jpeg', null, true);
  }

  /**
   * Upload image
   *
   * @return int
  */
  protected function uploadImage(): int {
    $form = [
      'image' => $this->getUploadedFile(),
    ];

    return $this->post(route('api.files'), $form)
      ->assertOk()
      ->json('media.id');
  }
}
