<?php

namespace App\Models\Categories;

use App\Models\Interfaces\Slugable;
use App\Models\Traits\SlugableTrait;
use ElasticScoutDriverPlus\CustomSearch;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Laravel\Scout\Searchable;
use Spatie\Translatable\HasTranslations;

class Category extends Model implements Slugable
{
    use SoftDeletes, HasTranslations, SlugableTrait, Searchable, CustomSearch;

    // Route to specific category
    public static $slugRoute = 'api.categories.slug';

    protected $fillable = ['name', 'icon_default', 'icon_selected'];

    /**
     * Translatable fields
    */
    public $translatable = [
      'name', 'slug'
    ];

    protected $visible = ['id', 'name', 'slug', 'parent_id', 'children', 'parent', 'is_baseline', 'is_shown'];

    protected $appends = ['is_shown'];

    /**
     * Method to search similar categories
     *
     * @param string $name
     *
     * @return Collection
    */
    public static function searchByName(string $name): Collection {
      return static::boolSearch()
        ->should(['wildcard' => ['name' => ['value' => "$name*"]]])
        ->should(['match' => ['name' => ['query' => $name]]])
        ->minimumShouldMatch(1)
        ->execute()
        ->models();
    }

    /**
     * Relation to parent category
     *
     * @return BelongsTo
    */
    public function parent(): BelongsTo {
      return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Relation to children
     *
     * @return HasMany
    */
    public function children(): HasMany {
      $relation = $this->hasMany(Category::class, 'parent_id');
      if ($this->is_baseline) {
        // Set impossible condition to prevent loading children
        $relation->whereNull('id');
      }
      return $relation;
    }

    /*
     * Scopes section
     */
    /**
     * Scope by parent
     *
     * @param Builder $query
     * @param int $categoryId
     *
     * @return Builder
    */
    public function scopeParent(Builder $query, int $categoryId): Builder {
      return $query->where('parent_id', $categoryId);
    }

    /**
     * Search by name
     *
     * @param Builder $query
     * @param string $keyword
     *
     * @return Builder
    */
    public function scopeKeyword(Builder $query, string $keyword): Builder {
      return $query->where('name', 'LIKE', "%$keyword%");
    }

    /**
     * Scope by full name
     *
     * @param Builder $query
     * @param string $name
     *
     * @return Builder
    */
    public function scopeName(Builder $query, string $name): Builder {
      return $query->where('name', 'like', "%\"$name\"%");
    }

    /**
     * Scope only top level categories
     *
     * @param Builder $query
     *
     * @return Builder
    */
    public function scopeTop(Builder $query): Builder {
      return $query->whereNull('parent_id');
    }

    /**
     * Scope only non-top level categories
     *
     * @param Builder $query
     *
     * @return Builder
    */
    public function scopeChild(Builder $query): Builder {
      return $query->whereNotNull('parent_id');
    }

    /**
     * Get array of searchable columns
     *
     * @return array
    */
    public function toSearchableArray(): array
    {
      return [
        'id' => $this->id,
        'parent_id' => $this->parent_id,
        'name' => $this->name,
      ];
    }

    /**
     * Attribute to show if category should be shown
     * It is calculated depending on is_baseline field of parent
     *
     * @return boolean
    */
    public function getIsShownAttribute(): bool {
      if ($this->parent_id && !$this->parent) {
        $this->load('parent');
      }

      return !$this->parent || !$this->parent->is_baseline;
    }
}
