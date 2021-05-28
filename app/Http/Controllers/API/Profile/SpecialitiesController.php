<?php

namespace App\Http\Controllers\API\Profile;

use App\Facades\MediaFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\Common\UpdateImageRequest;
use App\Http\Requests\Common\UploadImageRequest;
use App\Http\Requests\Profile\CreateMultipleSpecialityFormRequest;
use App\Http\Requests\Profile\CreateSpecialityFormRequest;
use App\Http\Requests\Profile\SearchSpecialityCategoriesController;
use App\Http\Requests\Profile\UpdateSpecialityFormRequest;
use App\Models\Categories\Category;
use App\Models\Media\Image;
use App\Models\User\Profile;
use App\Models\User\ProfileSpeciality;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SpecialitiesController extends Controller
{
  protected $category;

  public function __construct(Category $category)
  {
    $this->category = $category;
  }

  /**
   * Method to create speciality
   *
   * @param CreateSpecialityFormRequest $request
   *
   * @return JsonResponse
   */
  public function create(CreateSpecialityFormRequest $request): JsonResponse
  {
    // Check if user has profile
    $profile = $this->getProfile();

    // If not, return error
    if (!$profile) {
      return $this->returnError(__('User does not have profile'), 403);
    }

    // Check if category can be used to create
    /* @var ?Category $category */
    $category = $this->category::find($request->input('category_id'));
    if (!$category || $category->servicesCount > 0) {
      return $this->returnError(__('This category can not be used as service'), 403);
    }

    // Search if user has exact same speciality
    $exactSpec = $profile->specialities()
      ->exact($request->input('name'), $request->input('category_id'))
      ->first();

    // If yes, return error
    if ($exactSpec) {
      return $this->returnError(
        __('Similar speciality exists'),
        405,
        ['speciality' => $exactSpec]
      );
    }

    // Create speciality
    $speciality = $profile->addSpeciality(
      $request->input('category_id'),
      $request->input('price'),
      $request->input('name'),
      $request->input('description'),
    );

    // Return result
    return $this->returnSuccess([
      'speciality' => $speciality,
    ]);
  }

  /**
   * Method to get information about specialities
   *
   * @param Request $request
   *
   * @return JsonResponse
   */
  public function get(Request $request): JsonResponse
  {
    // Check if user has profile
    $profile = $this->getProfile();

    // If not, return error
    if (!$profile) {
      return $this->returnError(__('User does not have profile'), 403);
    }

    $query = $profile->specialities();
    if ($categoryId = $request->input('category_id')) {
      $query->category($categoryId);
    }

    $specialities = ProfileSpeciality::includeCategoriesPath($query->get());

    // Return all specialities of the profile
    return $this->returnSuccess([
      'specialities' => $specialities,
    ]);
  }

  /**
   * Method to get information about speciality by category id
   *
   * @param Category $category
   *
   * @return JsonResponse
  */
  public function getByCategory(Category $category): JsonResponse {
    // Check if user has profile
    $profile = $this->getProfile();

    // If not, return error
    if (!$profile) {
      return $this->returnError(__('User does not have profile'), 403);
    }

    $speciality = $profile->specialities()
      ->category($category->id)
      ->first();

    return $this->returnSuccess(compact('speciality'));
  }

  /**
   * Method to update speciality
   *
   * @param UpdateSpecialityFormRequest $request
   * @param int $specialityId
   *
   * @return JsonResponse
   */
  public function update(UpdateSpecialityFormRequest $request, int $specialityId): JsonResponse
  {
    // Check if user has profile
    $profile = $this->getProfile();

    // If not, return error
    if (!$profile) {
      return $this->returnError(__('User does not have profile'), 403);
    }

    // Get speciality by id
    $speciality = $profile->specialities()->find($specialityId);

    // Check if speciality exists
    if (!$speciality) {
      return $this->returnError(__("Speciality doesn't exists"), 403);
    }

    // Update speciality
    $speciality->updateInfo(
      $request->input('price'),
      $request->input('name'),
      $request->input('description')
    );

    // Add images
    $imagesToAdd = $request->file('images_add', []);
    foreach ($imagesToAdd as $file) {
      MediaFacade::upload(
        $file,
        null,
        ProfileSpeciality::class,
        $speciality->id
      );
    }

    // Remove images
    $imagesToRemove = $request->input('images_remove', []);
    if ($imagesToRemove) {
      $speciality->media()->whereIn('id', $imagesToRemove)->delete();
    }

    $speciality->load('media');

    // Return the result
    return $this->returnSuccess([
      'speciality' => $speciality,
    ]);
  }

  /**
   * Method to delete speciality
   *
   * @param int $specialityId
   *
   * @return JsonResponse
   *
   */
  public function delete(int $specialityId): JsonResponse
  {
    // Check if user has profile
    $profile = $this->getProfile();

    // If not, return error
    if (!$profile) {
      return $this->returnError(__('User does not have profile'), 403);
    }

    // Get speciality by id
    $speciality = $profile->specialities()->find($specialityId);
    // If exists, delete
    if ($speciality) {
      try {
        $speciality->delete();
      } catch (Exception $e) {
        Log::error("Error on deleting speciality ($specialityId) - {$e->getMessage()}");
      }
    }

    // Return result
    return $this->returnSuccess([
      'deleted' => !!$speciality,
    ]);
  }

  /**
   * Method to get profile of current user
   *
   * @return ?Profile
   */
  protected function getProfile(): ?Profile
  {
    $user = Auth::user();
    if (!$user) {
      return null;
    }

    return $user->profile()->first();
  }

  /**
   * Method to add image to speciality
   *
   * @param UploadImageRequest $request
   * @param int $specialityId
   *
   * @return JsonResponse
   */
  public function uploadImage(UploadImageRequest $request, int $specialityId): JsonResponse {
    $profile = $this->getProfile();
    if (!$profile) {
      return $this->returnError(__('No profile found'), 404);
    }
    $speciality = $profile->specialities()->find($specialityId);
    if (!$speciality) {
      return $this->returnError(__('Speciality not found'), 404);
    }

    // Check if the image limit is already exceeded
    if ($speciality->media()->count() >= config('app.specialities.maxImages')) {
      return $this->returnError(__('Image limit is exceeded'), 405);
    }

    $file = $request->file('image');
    try {
      $image = MediaFacade::upload(
        $file,
        null,
        ProfileSpeciality::class,
        $speciality->id
      );
    } catch (\Exception $exception) {
      return $this->returnError($exception->getMessage(), 505);
    }

    return $this->returnSuccess([
      'image' => $image,
    ]);
  }

  /**
   * Method to remove image from speciality
   *
   * @param int $specialityId
   * @param int $imageId
   *
   * @return JsonResponse
   */
  public function removeImage(int $specialityId, int $imageId): JsonResponse {
    $profile = $this->getProfile();
    if (!$profile) {
      return $this->returnError(__('No profile found'), 404);
    }
    $speciality = $profile->specialities()->find($specialityId);
    if (!$speciality) {
      return $this->returnError(__('Speciality not found'), 404);
    }

    $image = $speciality->media()->find($imageId);
    if (!$image) {
      return $this->returnError(__('Image not found'), 404);
    }
    $image->delete();

    return $this->returnSuccess();
  }

  /**
   * Method to remove image from speciality
   *
   * @param int $specialityId
   * @param int $imageId
   * @param UpdateImageRequest $request
   *
   * @return JsonResponse
   */
  public function updateImage(int $specialityId, int $imageId, UpdateImageRequest $request): JsonResponse {
    $profile = $this->getProfile();
    if (!$profile) {
      return $this->returnError(__('No profile found'), 404);
    }
    $speciality = $profile->specialities()->find($specialityId);
    if (!$speciality) {
      return $this->returnError(__('Speciality not found'), 404);
    }

    /* @var Image $image */
    $image = $speciality->media()->find($imageId);
    if (!$image) {
      return $this->returnError(__('Image not found'), 404);
    }
    $image->update($request->validated());

    return $this->returnSuccess([
      'image' => $image,
    ]);
  }

  /**
   * Route to get categories for specialities
   *
   * @return JsonResponse
  */
  public function getCategories(): JsonResponse {
    $profile = $this->getProfile();

    $categories = Category::query()->top()->get();
    $result = Category::addServicesFields($categories, $profile, null, false, false);
    return $this->returnSuccess(compact('result'));
  }

  /**
   * Route to get subcategories by parent id
   *
   * @param int $categoryId
   *
   * @return JsonResponse
  */
  public function getSubcategories(int $categoryId): JsonResponse {
    $profile = $this->getProfile();

    $subcategories = Category::query()
      ->parent($categoryId)
      ->get();

    $result = Category::addServicesFields($subcategories, $profile, $categoryId, true, true);

    return $this->returnSuccess(compact('result'));
  }

  /**
   * Route to search through the categories
   *
   * @param SearchSpecialityCategoriesController $request
   *
   * @return JsonResponse
  */
  public function searchCategories(SearchSpecialityCategoriesController $request): JsonResponse {
    // Get profile
    $profile = $this->getProfile();

    // Extract parameters
    $keyword = $request->input('keyword');
    $parentId = $request->input('parent_id');
    $size = $request->input('size', 15);

    // Search categories
    $categories = Category::searchByName($keyword, $parentId, $size);

    // Map categories
    $result = Category::addServicesFields($categories, $profile, $parentId, true, false);

    return $this->returnSuccess(compact('result'));
  }

  /**
   * Route to add all category services
   *
   * @param CreateMultipleSpecialityFormRequest $request
   * @param Category $category
   *
   * @return JsonResponse
  */
  public function createMultiple(CreateMultipleSpecialityFormRequest $request, Category $category): JsonResponse {
    // Check if user has profile
    $profile = $this->getProfile();

    // If not, return error
    if (!$profile) {
      return $this->returnError(__('User does not have profile'), 403);
    }

    // Search if user has exact same speciality
    $existingSpecialities = $profile->specialities()
      ->category($category->id)
      ->pluck('id');

    $services = $category->getServicesAttribute();
    if (sizeof($services) > 0) {
      $serviceIds = $services->pluck('id');
    } else {
      $serviceIds = [$category->id];
    }

    // Create speciality
    $specialities = [];
    foreach ($serviceIds as $categoryId) {
      if (!$existingSpecialities->contains($categoryId)) {
        $specialities[] = $profile->addSpeciality(
          $categoryId,
          $request->input('price'),
          $request->input('name'),
          $request->input('description'),
        );
      }
    }

    // Return result
    return $this->returnSuccess([
      'specialities' => $specialities,
    ]);
  }

  /**
   * Route to delete multiple specialities
   *
   * @param Category $category
   *
   * @return JsonResponse
  */
  public function deleteMultiple(Category $category): JsonResponse {
    // Check if user has profile
    $profile = $this->getProfile();

    // If not, return error
    if (!$profile) {
      return $this->returnError(__('User does not have profile'), 403);
    }

    $profile->specialities()
      ->category($category->id)
      ->delete();

    $specialities = $profile->specialities()->get();

    return $this->returnSuccess(['specialities' => $specialities]);
  }
}
