<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tests\TestCase;

class CardsTest extends TestCase
{
  use RefreshDatabase;
    /**
     * A basic test for checking if API works
     *
     * @return void
     */
    public function testAPI()
    {
      $this->fillDatabase();;

      // Try routes without login
      $this->get(route('api.user.cards.list'))
        ->assertStatus(401);
      $this->post(route('api.user.cards.create'), $this->getCreateForm())
        ->assertStatus(401);

      // Get user
      $user = User::query()->inRandomOrder()->first();

      Auth::login($user);

      // Check if he doesn't have cards
      $this->checkCount(0);

      // Create card
      $card = $this->post(route('api.user.cards.create'), $this->getCreateForm())
        ->assertOk()
        ->json('card');

      // Check the amount
      $this->checkCount(1);

      // Update the card
      $this->put(route('api.user.cards.update', ['cardId' => $card['id']]), $this->getUpdateForm())
        ->assertOk();

      // Delete the card
      $this->delete(route('api.user.cards.delete', ['cardId' => $card['id']]))
        ->assertOk();

      // Check amount
      $this->checkCount(0);

      // Try updating deleted card
      $this->put(route('api.user.cards.update', ['cardId' => $card['id']]), $this->getUpdateForm())
        ->assertStatus(403);

      $this->clearDatabase();
    }

    /**
     * Get creation form for credit card form
     *
     * @return array
    */
    public function getCreateForm(): array {
      return [
        'label' => Str::random(),
        'name' => Str::random(6)." ".Str::random(6),
        'number' => (string)rand(1e15, 9e15),
        'expiration_month' => rand(1, 12),
        'expiration_year' => rand(date('Y'), date('Y') + 4),
        'cvv' => rand(1e2, 9e2),
      ];
    }

    /**
     * Get creation form for getting edit card form
     *
     * @return array
    */
    public function getUpdateForm(): array {
      return [
        'label' => Str::random(),
        'expiration_month' => rand(1, 12),
        'expiration_year' => rand(date('Y'), date('Y') + 4),
      ];
    }

    /**
     * Method to check cards' count
     *
     * @param int $count
    */
    public function checkCount(int $count) {
      $cards = $this->get(route('api.user.cards.list'))->assertOk()->json('cards');
      $this->assertEquals($count, sizeof($cards));
    }
}
