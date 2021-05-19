<?php

namespace Tests\Feature;

use App\Facades\PhoneVerificationFacade;
use App\Models\Authentication\Bot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class BotLoginTest extends TestCase
{
  use RefreshDatabase;

  /**
   * Function to create a bot
   * Returns the token
   *
   * @return string
  */
  protected function createBot(): string {
    $token = Str::random();
    Bot::createBot($token);
    return $token;
  }

  /**
   * Test bot login routes
   *
   * @return void
  */
  public function testStory() {
    // Create a bot
    $botToken = $this->createBot();

    // Send request to create token
    $phoneNumber = "7123151256";
    $loginToken = $this
      ->withHeaders(['API-TOKEN' => $botToken])
      ->post(route('bot.tokens.create'), ['phone' => $phoneNumber])
      ->assertOk()
      ->json('token');

    // Use token to verify the user
    $verificationUuid = $this->post(route('api.tokens.verify', ['uuid' => $loginToken]))
      ->assertOk()
      ->json(['verification_uuid']);

    // Check the verified uuid
    $this->assertNotNull(PhoneVerificationFacade::getVerifiedPhone($verificationUuid));
  }
}
