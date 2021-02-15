<?php


namespace App\Models\Traits;


use Illuminate\Database\Eloquent\Builder;

trait SlugableTrait
{

    public function getSlugColumn(): string
    {
        return 'name';
    }

    public function getSlugTranslatable(): bool
    {
        return true;
    }

    public function scopeSlug(Builder $query, string $slug): Builder {
        if ($this->getSlugTranslatable()) {
            return $query->where('slug', 'like', "%\"$slug\"%");
        } else {
            return $query->where('slug', $slug);
        }
    }

    /**
     * Attribute to generate links
     *
     * @return string
    */

    public function getSlugLinkAttribute(): string {
        return route($this::$slugRoute, ['slug' => $this->slug]);
    }
}
