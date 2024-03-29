<?php

namespace App\Http\Controllers\API\User\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\Views\AddViewRequest;
use App\Models\Profile\ProfileView;
use App\Models\User;
use App\Models\User\Profile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ViewsController extends Controller
{

  /**
   * Method to add view
   *
   * @param AddViewRequest $request
   * @param Profile $profile
   *
   * @return JsonResponse
   */
  public function add(AddViewRequest $request, Profile $profile): JsonResponse {
    /* @var User $user */
    $user = Auth::user();

    if ($user && $user->id == $profile->user_id) {
      return $this->returnError(__("You can't view own profile"), 403);
    }

    $view = ProfileView::make($profile, $user, $request->ip(), $request->input('opened', false));

    $profile->synchronizeViews();

    return $this->returnSuccess([
      'view' => $view,
    ]);
  }
}
