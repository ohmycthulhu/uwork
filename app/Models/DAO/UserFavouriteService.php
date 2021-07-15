<?php

namespace App\Models\DAO;

use App\Facades\SearchFacade;
use App\Models\User\ProfileSpeciality;
use ElasticScoutDriverPlus\CustomSearch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;

/**
 * Class for importing data and searching within favourite services
 */
class UserFavouriteService extends DAO
{
  use Searchable, CustomSearch;

  protected $table = 'user_favourite_services';

  protected $fillable = ['service_id'];

  /**
   * Relation to service
   *
   * @return BelongsTo
   */
  public function service(): BelongsTo
  {
    return $this->belongsTo(ProfileSpeciality::class, 'service_id');
  }

  /**
   * Function to convert the row into index
   *
   * @return array
   */
  public function toSearchableArray(): array
  {
    $this->load('service.profile.district');

    if (!$this->service || !$this->service->profile) {
      return [];
    }

    $speciality = $this->service;
    $profile = $speciality->profile;

    return [
      'userId' => $this->user_id,
      'speciality' => [
        'price' => $speciality->price,
        'categoryId' => $speciality->category_id,
        'catPath' => SearchFacade::calculateCategoryPath($speciality->category_id),
      ],
      'profile' => [
        'regionId' => $profile->region_id,
        'cityId' => $profile->city_id,
        'districtId' => $profile->district_id,
        'district' => $profile->district ? $profile->district->name : null,
        'subwayId' => $profile->subway_id,
        'rating' => $profile->rating,
      ]
    ];
  }

  /**
   * Scope to query by specific service id
   *
   * @param Builder $query
   * @param int $serviceId
   *
   * @return Builder
  */
  public function scopeServiceId(Builder $query, int $serviceId): Builder {
    return $query->where('service_id', $serviceId);
  }
}
