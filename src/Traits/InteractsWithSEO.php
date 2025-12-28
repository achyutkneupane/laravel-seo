<?php

declare(strict_types=1);

namespace LaravelSEO\Traits;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use LaravelSEO\Models\SEO;

trait InteractsWithSEO
{
    protected string $titleColumn = 'title';

    protected function titleColumn(): string
    {
        return $this->titleColumn;
    }

    protected function getTitleValue(): ?string
    {
        return data_get($this, $this->titleColumn());
    }

    public static function bootInteractsWithSEO(): void
    {
        static::created(function (self $model): self {
            $authUser = auth()->check() ? auth()->user() : null;
            $defaultName = config('app.name', 'SEO Writer');
            $userName = $authUser ? $authUser->name : $defaultName;

            SEO::query()
                ->updateOrCreate([
                    'seoable_id' => $model->id,
                    'seoable_type' => $model::class,
                ], [
                    'meta_title' => $model->title ?? $model->name,
                    'og_title' => $model->title ?? $model->name,
                    'meta_description' => $model->description ?? null,
                    'og_description' => $model->description ?? null,
                    'author' => $userName,
                    'publisher' => $defaultName,
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
