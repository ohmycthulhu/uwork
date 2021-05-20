<?php

namespace App\Models\Categories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CategoryService extends Model
{
    /**
     * Scope for filtering by category id
     *
     * @param Builder $query
     * @param int $categoryId
     *
     * @return Builder
    */
    public function scopeCategory(Builder $query, int $categoryId): Builder {
      return $query->where('category_path', 'LIKE', "% $categoryId %");
    }
}
