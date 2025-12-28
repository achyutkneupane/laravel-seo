<?php

declare(strict_types=1);

namespace LaravelSEO\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use LaravelSEO\Models\SEO;

trait InteractsWithSEO
{
    public static function boot(): void
    {
        parent::boot();

        static::created(function (Model $model): Model {
            SEO::query()
                ->updateOrCreate([
                    'seoable_id' => $model->getKey(),
                    'seoable_type' => $model::class,
                ], [
                    'meta_title' => $model->getTitleValue(),
                    'og_title' => $model->getTitleValue(),
                    'meta_description' => $model->getDescriptionValue(),
                    'og_description' => $model->getDescriptionValue(),
                    'meta_keywords' => $model->getTagsValue(),
                    'author' => $model->getAuthorValue(),
                    'publisher' => $model->getPublisherValue(),
                    'robots' => ['index', 'follow'],
                ]);

            return $model;
        });
    }

    /**
     * @return MorphOne<SEO>
     */
    public function seo(): MorphOne
    {
        return $this->morphOne(SEO::class, 'model');
    }
}
