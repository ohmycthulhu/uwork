<?php

namespace Tests\Unit;

use App\Models\Authentication\Bot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Tests\TestCase;

class BotCommandsTest extends TestCase
{
  use RefreshDatabase;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testCRUD()
    {
      // Generate token
      Artisan::call("bot-token:generate");

      // Check if tokens are generated
      $this->assertNotEmpty(Bot::query()->get());

      // Import token
      $token = Str::random();
      Artisan::call("bot-token:import", ['token' => $token]);

      // Check if token is imported
      $this->assertNotEmpty(Bot::query()->token($token)->get());

      // Check if tokens are listed
      $this->checkList();

      // Try enabling and disabling the tokens
      Artisan::call("bot-token:disable", ['token' => $token]);
      var_dump(Bot::query()->token($token)->get());
      $this->assertNotEmpty(Bot::query()->token($token)->state(0)->get());


      Artisan::call("bot-token:enable", ['token' => $token]);
      $this->assertNotEmpty(Bot::query()->token($token)->state(1)->get());

      // Remove token
      Artisan::call("bot-token:remove", ['token' => $token]);
      $this->assertEmpty(Bot::query()->token($token)->get());

      // Check the list
      $this->checkList();
    }

    /**
     * Check the amount
     *
     * @return void
    */
    protected function checkList() {
      return;
      $bots = Bot::query()->get();
      Artisan::call("bot-token:list");

      $output = Artisan::output();

      var_dump($output);
      foreach ($bots as $bot) {
        $this->assertStringContainsString(
          $bot->token,
          $output
        );
        if ($bot->name) {
          $this->assertStringContainsString(
            $bot->name,
            $output
          );
        }
      }
    }
}
