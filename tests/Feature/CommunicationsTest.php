<?php

namespace Tests\Feature;

use App\Models\Communication\Appeal;
use App\Models\Communication\AppealReason;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class CommunicationsTest extends TestCase
{
  use RefreshDatabase;

  /**
   * A test to check appeals.
   *
   * @return void
   */
  public function testAppealModels()
  {
    // Create appeal reasons
    $appealReasons = factory(AppealReason::class, 5)->create();

    // Ensure they are created
    $this->assertDatabaseCount('appeal_reasons', 5);

    // Create several users
    $users = factory(User::class, 4)->create();

    $amount = 0;
    // For each appeal reason, try creating new appeals for each user
    foreach ($users as $user) {
      $amount++;
      $reason = rand(1, 100) > 90 ? $appealReasons->random()->id : null;
      $form = factory(Appeal::class)->make();

      try {
        Appeal::instantiate(
          $form->text,
          $reason,
          $form->appeal_reason_other,
          $user,
          $form->name,
          $form->ip,
          $form->phone,
          $form->email
        );
      } catch (Exception $exception) {
          var_dump($exception);
          $this->assertFalse(true);
      }
      $this->assertDatabaseCount('appeals', $amount);
    }

    // Try to create appeal without user
    $amount++;
    $form = factory(Appeal::class)->make();
    $reason = $appealReasons->random()->id;
    try {
      Appeal::instantiate(
        $form->text,
        $reason,
        $form->appeal_reason_other,
        null,
        $form->name,
        $form->ip,
        $form->phone,
        $form->email
      );
    } catch (Exception $exception) {
      var_dump($exception);
      $this->assertFalse(true);
    }
    // Try to spam by creating appeals
    try {
      $form = factory(Appeal::class)->make();
      $user = $users->random();

      for ($i = 0; $i < 10; $i++) {
        Appeal::instantiate(
          $form->text,
          $reason,
          $form->appeal_reason_other,
          $user,
          $form->name,
          $form->ip,
          $form->phone,
          $form->email
        );
        $amount++;
      }

      $this->assertFalse(true);
    } catch (Exception $e) {
      $this->assertNotNull($e);
    }

    // Delete users
    $users->each(function ($u) {
      $u->forceDelete();
    });

    // Delete appeal reasons
    $appealReasons->each(function ($r) { $r->forceDelete(); });

    // Ensure appeals are stored
    $this->assertDatabaseCount('appeals', $amount);
  }

  /**
   * Test for testing an API
   *
   * @return void
  */
  public function testAPI() {
    // Create reasons and users
    $reason = factory(AppealReason::class)->create();
    $user = factory(User::class)->create();

    $this->get(route('api.appealReasons'))
      ->assertOk()
      ->assertJsonStructure(['reasons']);

    // Try sending partial requests with no user
    $form = factory(Appeal::class)->make()->toArray();
    $form['appeal_reason_id'] = $reason->id;
    $this->sendPartialRequests(
      route('api.appeals.create'),
      $form,
      ['text', 'phone', 'name']
    );

    Auth::login($user);
    $this->sendPartialRequests(
      route('api.appeals.create'),
      $form,
      ['text']
    );

    // Send request and check if appeal is created
    $this->post(route('api.appeals.create'), $form)
      ->assertOk();

    $this->assertDatabaseCount('appeals', 1);


    // Try spamming
    for ($i = 0; $i < 10; $i++) {
      $this->post(route('api.appeals.create'), $form);
    }
    $this->post(route('api.appeals.create'), $form)
      ->assertStatus(405);

    // Delete reasons and users
    $reason->forceDelete();
    $user->forceDelete();
  }
}
