<?php

namespace App\Http\Controllers;

use App\Facades\NotificationFacade;
use App\Http\Requests\ReadNotificationsRequest;
use App\Http\Requests\RetrieveNotificationsRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
  /**
   * Method that returns all notifications of person
   *
   * @param RetrieveNotificationsRequest $request
   *
   * @return JsonResponse
  */
  public function get(RetrieveNotificationsRequest $request): JsonResponse {
    // Get user
    /* @var User $user */
    $user = Auth::user();

    // Get notifications
    $notifications = NotificationFacade::getByUser(
      $user,
      $request->input('unread_only', false),
      $request->input('amount', 15)
    );

    // Return response
    return $this->returnSuccess(compact('notifications'));
  }

  /**
   * Method that marks notifications as read
   *
   * @param ReadNotificationsRequest $request
   *
   * @return JsonResponse
  */
  public function markRead(ReadNotificationsRequest $request): JsonResponse {
    // Get user
    /* @var User $user */
    $user = Auth::user();

    // Mark notifications
    $count = NotificationFacade::markRead(
      $user,
      $request->input('ids')
    );

    // Return success
    return $this->returnSuccess(compact('count'));
  }

}
