<?php

namespace Tests\Feature;

use App\Models\Complaints\ComplaintType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ComplaintsTest extends TestCase
{
  use RefreshDatabase;

  /**
   * Method to test database
   *
   * @return void
  */
  public function testDatabase() {
    // Create user and profile
    $profile = $this->createProfile($this->createUser());

    // Create another user
    $user = $this->createUser();

    $complaintTypes = factory(ComplaintType::class, 5)->create();

    // Try creating complaint
    $this->assertNotNull($profile->createComplaint(
      $user,
      null,
      $complaintTypes->random()->id,
      null,
      'Some text'
    ));

    // Check amount of complaints
    $this->assertDatabaseCount('complaints', 1);
    $this->assertEquals(1, $profile->complaints()->count());

    $this->assertNull($profile->createComplaint(
      $user,
      null,
      $complaintTypes->random()->id,
      null,
      'Some text'
    ));

    // Check amount of complaints
    $this->assertEquals(1, $profile->complaints()->count());
  }

  /**
   * Method to test endpoints
   *
   * @return void
  */
  public function testEndpoints() {
    // Create user and profile
    $userOwner = $this->createUser();
    $profile = $this->createProfile($userOwner);
    $complaintTypes = factory(ComplaintType::class, 5)->create();

    // Create another user
    $userOther = $this->createUser();

    // Try to complain to self profile
    Auth::login($userOwner);
    $this->post(route('api.profiles.complaints.create', ['profile' => $profile->id]), [
      'type_id' => $complaintTypes->random()->id,
      'text' => 'Some text'
    ])->assertStatus(403);

    // Try complaining on profile
    Auth::login($userOther);
    for ($i = 0; $i < 2; $i++) {
      $this->post(route('api.profiles.complaints.create', ['profile' => $profile->id]), [
        'type_id' => $complaintTypes->random()->id,
        'text' => 'Some other text'
      ])->assertStatus($i ? 403 : 200);
      $this->assertEquals(
        1,
        $profile->complaints()->count()
      );
    }
  }
}
