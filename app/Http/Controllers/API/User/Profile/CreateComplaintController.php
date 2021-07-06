<?php

namespace App\Http\Controllers\API\User\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\CreateComplaintRequest;
use App\Models\User;
use App\Models\User\Profile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreateComplaintController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  CreateComplaintRequest  $request
     * @param Profile $profile
     *
     * @return JsonResponse
     */
    public function __invoke(CreateComplaintRequest $request, Profile $profile): JsonResponse
    {
      /* @var User $user */
      $user = Auth::user();

      if ($user && $user->id == $profile->user_id) {
        return $this->returnError('You can\'t complaint to own profile', 403);
      }

      $complaint = $profile->createComplaint(
        $user,
        $request->ip(),
        $request->input('type_id'),
        $request->input('reason_other'),
        $request->input('text')
      );

      if ($complaint) {
        // Return success if could create
        return $this->returnSuccess(compact($complaint));
      } else {
        // Otherwise, return error
        return $this->returnError(__('Error on creating complaint'), 403);
      }
    }
}
