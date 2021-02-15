<?php

namespace App\Observers;

use App\Mail\NewsCreatedMail;
use App\Models\Interfaces\Slugable;
use App\Models\News;
use App\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Slug;

class SlugableObserver
{
    /**
     * Handle the news "created" event.
     *
     * @param  \App\Models\Interfaces\Slugable $model
     * @return void
     */
    public function created(Slugable $model)
    {
        if (!$model->slug) {
            if ($model->getSlugTranslatable()) {
                $slugs = array_map(function ($headline) use ($model) {
                    return $this->generateSlug($model, $headline);
                }, $model->getTranslations($model->getSlugColumn()));
                $slug = $slugs;
            } else {
                $slug = $this->generateSlug($model, $model[$model->getSlugColumn()] ?? '');
            }
            $model->slug = $slug;
            $model->save();
        }
    }

    public function generateSlug(Slugable $model, string $headline): string {
        $exampleSlug = Str::slug($headline);
        $result = $exampleSlug;
        while ($model::query()->slug($result)->first() != null) {
            $result .= rand(0, 9);
        }
        return $result;
    }
}
