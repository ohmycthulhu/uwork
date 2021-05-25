<?php

namespace Tests\Feature;

use App\Models\Info\TextStatistic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TextStatisticsTest extends TestCase
{
  use RefreshDatabase;
  /**
   * Method to test text statistics
   *
   * @return void
  */
  public function testModels() {
    // Get possible text types
    $infoTypes = config('info.texts');

    $this->assertNotEmpty($infoTypes);

    // Get amount of each text type and check if it's zero
    foreach ($infoTypes as $type) {
      $statistic = TextStatistic::query()
        ->type($type)
        ->first();
      $this->assertNull($statistic);
    }

    // Select random type
    $type = $infoTypes[array_rand($infoTypes)];

    $amount = 10;
    // Increment the amount
    for ($i = 1; $i <= $amount; $i++) {
      $this->assertEquals($i, TextStatistic::incrementByType($type));
    }

    // Check the amount
    $statistic = TextStatistic::query()->type($type)->first();
    $this->assertEquals($amount, $statistic->total);

    // Decrease the amount
    for ($j = 1; $j <= $amount; $j++) {
      $this->assertEquals($j, TextStatistic::decrementByType($type));
    }

    // Check the amount
    $statistic = TextStatistic::query()->type($type)->first();
    $this->assertEquals(0, $statistic->total);
  }

  /**
   * Method to test routes
   *
   * @return void
  */
  public function testRoutes() {
    $infoTypes = config('info.texts');

    foreach ($infoTypes as $type) {
      $this->get(route('api.texts.get', ['type' => $type]))
        ->assertOk();
    }

    $type = $infoTypes[array_rand($infoTypes)];

    for ($i = 1; $i <= 10; $i++) {
      $this->assertEquals($i, $this->post(route('api.texts.upvote', compact('type')))
        ->assertOk()
        ->json('amount'));
    }

    for ($i = 1; $i <= 10; $i++) {
      $this->assertEquals($i, $this->delete(route('api.texts.downvote', compact('type')))
        ->assertOk()
        ->json('amount'));
    }
  }
}
