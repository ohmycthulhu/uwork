<?php

namespace App\Observers;

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
        $profileSpeciality->category_path = $this->calculateCategoryPath($profileSpeciality->category_id);
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
        $profileSpeciality->category_path = $this->calculateCategoryPath($profileSpeciality->category_id);
//        $profileSpeciality->save();
      }
    }

    /**
     * Method to calculate category path
     *
     * @param int $categoryId
     *
     * @return string
    */
    protected function calculateCategoryPath(int $categoryId): string {
      $category = Category::query()->find($categoryId);
      if (!$category) {
        return '';
      }
      $lastCategoryId = $categoryId;
      $result = '';
      do {
        $result = "|$lastCategoryId|$result";
        $category = $category->parent()->first();
        $lastCategoryId = $category ? $category->id : null;
      } while ($lastCategoryId);

      return $result;
    }
}
