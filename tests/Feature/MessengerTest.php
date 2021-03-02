<?php

namespace Tests\Feature;

use App\Models\Messenger\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Tests\TestCase;

class MessengerTest extends TestCase
{
  use RefreshDatabase;

  /**
   * Method to test messenger endpoints
   *
   * @return void
   */
  public function testEndpoints()
  {
    // Fill database
    $this->fillDatabase();

    // Get some users
    $user = $this->createUser();
    $anotherUser = $this->createUser();

    $messageForm = $this->getCreationForm();

    // Try to send message without authentication
    $this->post(route('api.chats.create', ['user' => $anotherUser->id]), $messageForm)
      ->assertStatus(401);

    // Try to send message to the same user
    Auth::login($user);
    $this->post(route('api.chats.create', ['user' => $user->id]), $messageForm)
      ->assertStatus(403);

    // Send message to the user
    $this->post(route('api.chats.create', ['user' => $anotherUser->id]), $messageForm)
      ->assertOk();

    // Ensure count of the messages
    $this->assertEquals(
      1,
      $this->get(route('api.chats.get', ['user' => $anotherUser->id]))
        ->assertOk()
        ->json('messages.total')
    );

    // Search in chat
//    $keyword = substr($messageForm['text'], 0, 4);
//
//    // Ensure amount of found messages
//    $this->assertEquals(
//      1,
//      $this->get(route('api.chats.search', ['user' => $anotherUser->id, 'keyword' => $keyword]))
//        ->assertOk()
//        ->json('messages.total')
//    );

    // Delete chat
    $this->delete(route('api.chats.delete', ['user' => $anotherUser->id]))
      ->assertOk();

    // Check existence of chat
    $this->get(route('api.chats.get', ['user' => $anotherUser->id]))
      ->assertStatus(403);

    // Clear database
    $this->clearDatabase();
  }

  /**
   * Returns creation form
   *
   * @return array
   */
  public function getCreationForm(): array
  {
    $basePath = storage_path('test/image.jpg');
    $path = storage_path('test/image_tmp.jpg');
    $fileName = Str::random().'.jpg';
    File::copy($basePath, $path);
    $file = new UploadedFile($path, $fileName, filesize($path), null, true);
    return [
      'text' => Str::random(),
      'attachment' => $file,
    ];
  }
}
