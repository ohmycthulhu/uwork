<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Messenger\SearchMessageRequest;
use App\Http\Requests\Messenger\SendMessageRequest;
use App\Models\Messenger\Chat;
use App\Models\Messenger\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class MessengerController extends Controller
{
  /**
   * Chat model instance
   *
   * @var Chat
   */
  protected $chat;
  /**
   * Message model instance
   *
   * @var Message
   */
  protected $message;

  /**
   * Creates instance of controller
   *
   * @param Chat $chat
   * @param Message $message
   */
  public function __construct(Chat $chat, Message $message)
  {
    $this->chat = $chat;
    $this->message = $message;
  }

  /**
   * Method to get all chats
   *
   * @return JsonResponse
  */
  public function getChats(): JsonResponse {
    $chats = $this->chat::user(Auth::user())->get();
    return response()->json([
      'chats' => $chats
    ]);
  }

  /**
   * Method to send the message
   *
   * @param SendMessageRequest $request
   * @param User $user
   *
   * @return JsonResponse
   */
  public function sendMessage(SendMessageRequest $request, User $user): JsonResponse
  {
    $currentUser = Auth::user();

    if ($currentUser->id == $user->id) {
      return response()->json([
        'error' => 'You can not sent message to yourself'
      ], 403);
    }

    // Manage the chat
    $chat = $this->chat::query()->user($currentUser)->user($user)->withTrashed()->first();

    if (!$chat) {
      $chat = $this->chat::make($currentUser, $user);
    }
    if ($chat->trashed()) {
      $chat->restore();
    }

    // Send message
    $message = $chat->sendMessage($request->input('text'), $request->file('attachment'));

    return response()->json([
      'status' => 'success',
      'message' => $message,
    ]);
  }

  /**
   * Method to delete chat
   *
   * @param User $user
   *
   * @return JsonResponse
   */
  public function deleteChat(User $user): JsonResponse
  {
    $chat = $this->chat::user($user)->user(Auth::user())->first();
    if ($chat) {
      $chat->delete();
    }
    return response()->json([
      'status' => 'success',
      'deleted' => !!$chat,
    ]);
  }

  /**
   * Method to get messages of the chat
   *
   * @param User $user
   *
   * @return JsonResponse
   */
  public function getMessages(User $user): JsonResponse
  {
    $chat = $this->chat::user($user)->user(Auth::user())->first();

    if (!$chat) {
      return response()->json([
        'error' => 'Chat doesn\'t exist',
      ], 403);
    }
    $messages = $chat->messages()->paginate(20);

    return response()->json([
      'messages' => $messages,
    ]);
  }

  /**
   * Method to search through messages in profile
   *
   * @param SearchMessageRequest $request
   * @param User $user
   *
   * @return JsonResponse
   */
  public function search(SearchMessageRequest $request, User $user): JsonResponse
  {
    $chat = $this->chat::user($user)->user(Auth::user())->first();

    if (!$chat) {
      return response()->json([
        'error' => 'Chat doesn\'t exist',
      ], 403);
    }

    $messages = $this->message::search("*".$request->input('keyword', '')."*")
      ->where('chat_id', $chat->id)
      ->paginate(20);

    return response()->json([
      'messages' => $messages,
      'keyword' => "*".$request->input('keyword', '')."*"
    ]);
  }
}
