<?php

namespace Tests\Feature;

use App\Facades\NotificationFacade;
use App\Models\User\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
  use RefreshDatabase;

  /** Amount of notifications for testing
   * @var int $notificationsCount
   */
  protected $notificationsCount = 10;

  /**
   * Test facade and database
   *
   * @return void
   */
  public function testFacade()
  {
    // Create user and profile
    $user = $this->createUser();
    $profile = $this->createProfile($user);

    // Create notifications
    for ($i = 0; $i < $this->notificationsCount; $i++) {
      NotificationFacade::create(
        $user,
        Profile::class,
        $profile->id,
        'Example',
        'Example'
      );
    }
    $this->assertEquals($this->notificationsCount, $user->notifications()->count());

    // Get notifications through facade and check the amount
    $this->assertEquals(
      $user->notifications()->count(),
      sizeof(NotificationFacade::getByUser($user)->items())
    );

    // Check if unread notifications are returned correctly
    $this->assertEquals(
      $user->notifications()->unread()->count(),
      sizeof(NotificationFacade::getByUser($user, true)->items())
    );

    // Mark some notifications as read
    $ids = $user->notifications()->unread()->inRandomOrder()->pluck('id')->toArray();
    $this->assertEquals(
      sizeof($ids),
      NotificationFacade::markRead($user, $ids)
    );

    // Check if they are read
    $this->assertEquals(
      0,
      $user->notifications()->ids($ids)->unread()->count()
    );

    // Mark everything read
    NotificationFacade::markRead($user, null);

    // Check unread count
    $this->assertEquals(
      0,
      $user->notifications()->unread()->count()
    );
  }

  /**
   * Test API endpoints
   *
   * @return void
   */
  public function testEndpoints()
  {
    // Create user, profile and notifications
    $user = $this->createUser();
    $profile = $this->createProfile($user);

    for ($i = 0; $i < $this->notificationsCount; $i++) {
      NotificationFacade::create(
        $user,
        Profile::class,
        $profile->id,
        'Example',
        'Example'
      );
    }

    $this->get(route('api.user.notifications.get'))
      ->assertStatus(401);

    Auth::login($user);
    // Retrieve notifications and check the amount
    $this->assertEquals(
      $this->notificationsCount,
      $this->get(route('api.user.notifications.get'))
        ->assertOk()
        ->json('notifications.total')
    );

    // Read several notifications
    $ids = $user->notifications()->unread()->inRandomOrder()->pluck('id')->toArray();
    $this->assertEquals(
      sizeof($ids),
      $this->post(route('api.user.notifications.read'), ['ids' => $ids])
      ->assertOk()
      ->json('count'));

    // Check notifications count
    $this->assertEquals(
      $user->notifications()->unread()->count(),
      $this->get(route('api.user.notifications.get', ['unread_only' => true]))
      ->assertOk()
      ->json('notifications.total')
    );

    // Read all notifications
    $this->post(route('api.user.notifications.read'))
      ->assertOk();

    // Check unread notifications count
    $this->assertEquals(
      0,
      $user->notifications()->unread()->count()
    );
  }
}
