<?php

namespace App\Observers;

use App\Facades\SearchFacade;
use App\Models\Categories\Category;
use App\Models\User\ProfileSpeciality;

class ProfileSpecialityObserver
{
    /**
     * Handle the profile speciality "created" event.
     *
     * @param ProfileSpeciality $profileSpeciality
     * @return void
     */
    public function created(ProfileSpeciality $profileSpeciality)
    {
      if (!$profileSpeciality->category_path) {
        $profileSpeciality->category_path = SearchFacade::calculateCategoryPath($profileSpeciality->category_id);
        $profileSpeciality->save();
      }
    }

    /**
     * Handle the profile speciality "updated" event.
     *
     * @param ProfileSpeciality $profileSpeciality
     * @return void
     */
    public function updating(ProfileSpeciality $profileSpeciality)
    {
      if ($profileSpeciality->isDirty('category_id')) {
        $profileSpeciality->category_path = SearchFacade::calculateCategoryPath($profileSpeciality->category_id);
//        $profileSpeciality->save();
      }
    }
}
