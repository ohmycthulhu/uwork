<?php

namespace Tests\Feature;

use App\Models\Profile\ProfileView;
use App\Models\Profile\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ReviewsTest extends TestCase
{
  use RefreshDatabase;

  /**
   * Method to test creating and synchronization of ratings
   *
   * @return void
   */
  public function testDatabase()
  {
    // Create user, categories, profile
    $user = $this->createUser();
    $anotherUser = $this->createUser();
    $profile = $this->createProfile($user);

    // Create ratings
    $profile->reviews()->createMany(
      factory(Review::class, 5)
        ->make(['user_id' => $anotherUser->id])
        ->toArray()
    );

    // Synchronize
    $profile->synchronizeReviews();

    // Check database
    $this->assertEquals(5, $profile->reviews_count);
    $this->assertEquals($profile->reviews()->sum('rating'), 5 * $profile->rating);

    // Add views
    $this->assertNull(ProfileView::make($profile, null, null));
    $v = ProfileView::make($profile, $anotherUser, null);
    $this->assertFalse(!!$v->opened);

    $profile->synchronizeViews();

    $this->assertDatabaseCount('profile_views', 1);
    $this->assertEquals(1, $profile->views_count);
    $this->assertEquals(0, $profile->open_count);

    // Check adding views
    $v2 = ProfileView::make($profile, $anotherUser, null, true);
    $this->assertEquals($v->id, $v2->id);
    $this->assertTrue(!!$v2->opened);

    // Synchronize views
    $profile->synchronizeViews();

    // Check amount of views
    $this->assertDatabaseCount('profile_views', 1);
    $this->assertEquals(1, $profile->views_count);
    $this->assertEquals(1, $profile->open_count);

    // Delete user
    $user->forceDelete();
  }
}
