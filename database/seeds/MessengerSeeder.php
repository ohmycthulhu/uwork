<?php

use Illuminate\Database\Seeder;

class MessengerSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    // Get list of users
    $users = \App\Models\User::query()->inRandomOrder()->take(20)->get();

    // Shuffle and divide into two groups
    $sliceIndex = floor(sizeof($users) / 2);
    $senders = $users->slice(0, $sliceIndex);
    $receivers = $users->slice($sliceIndex);

    // For each user in first group
    foreach ($senders as $sender) {
      // And each user in second group
      foreach ($receivers as $receiver) {
        // Create chat
        $chat = factory(\App\Models\Messenger\Chat::class)
          ->create([
            'initiator_id' => $sender->id,
            'acceptor_id' => $receiver->id,
          ]);

        // Create messages
        for ($i = 0; $i < 10; $i++) {
          $chat->messages()
            ->create(factory(\App\Models\Messenger\Message::class)
              ->make(['user_id' => rand() % 2 == 0 ? $sender->id : $receiver->id])
              ->toArray()
            );
        }
      }
    }
  }
}
