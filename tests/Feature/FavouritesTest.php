<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\User\ProfileSpeciality;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class FavouritesTest extends TestCase
{
  use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testFavourites()
    {
      $this->fillDatabase();

      // Get some speciality
      $service = ProfileSpeciality::query()
        ->inRandomOrder()
        ->firsT();
      $createdBy = $service->profile()->first()->user_id;

      // Try to add without login
      $this->post(route('api.user.fav.add', ['serviceId' => $service->id]))
        ->assertStatus(401);

      // Login via user
      $user = User::query()
        ->whereNotIn('id', [$createdBy])
        ->inRandomOrder()
        ->first();
      Auth::login($user);

      // Try to add to favourite
      $this->post(route('api.user.fav.add', ['serviceId' => $service->id]))
        ->assertOk();

      $this->assertFavsCount($user, 1);

      // Remove from favourites
      $this->delete(route('api.user.fav.remove', ['serviceId' => $service->id]))
        ->assertOk();

      // Get favourites and check if it is empty
      $this->assertFavsCount($user, 0);

      // Login with user that is associated with the service
      Auth::login(User::find($createdBy));

      // Try to add to favourite
      $this->post(route('api.user.fav.add', ['serviceId' => $service->id]))
        ->assertStatus(403);

      $this->clearDatabase();;
    }

    /**
     * Test the amount of favourites
     *
     * @param User $user
     * @param int $count
    */
    protected function assertFavsCount(User $user, int $count) {
      $this->assertEquals($count, $user->favouriteServices()->count());

      // Get favourites and check if they are same
      $response = $this->get(route('api.user.fav.list'))
        ->assertOk();
      var_dump($response->content());
      $servicesCount = $response->json('services.total');
      $this->assertEquals($count, $servicesCount);
    }
}
