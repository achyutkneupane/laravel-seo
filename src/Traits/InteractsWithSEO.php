<?php

declare(strict_types=1);

namespace LaravelSEO\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Collection;
use LaravelSEO\Models\SEO;
use RalphJSmit\Laravel\SEO\Schema\ArticleSchema;
use RalphJSmit\Laravel\SEO\SchemaCollection;
use RalphJSmit\Laravel\SEO\Support\SEOData;

trait InteractsWithSEO
{
    use HasColumns;

    public static function bootInteractsWithSEO(): void
    {
        /**
         * @param Model|HasColumns $model
         */
        static::created(function (Model $model): Model {
            SEO::query()
                ->updateOrCreate([
                    'model_id' => $model->getKey(),
                    'model_type' => $model::class,
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

    public function getDynamicSEOData(): SEOData
    {
        $seo = $this->seo;

        $title = $seo->meta_title ?? $this->getTitleValue() ?? 'Untitled';
        $description = $seo->meta_description ?? $this->getDescriptionValue() ?? 'No description available.';
        $url = $seo->canonical ?? $this->getUrlValue() ?? null;
        $category = $this->getCategoryValue() ?? 'Blog';
        $tags = $seo->meta_keywords ?? $this->getTagsValue() ?? [];
        $author = $seo->author ?? $this->getAuthorValue() ?? 'Unknown Author';
        $publisher = $seo->publisher ?? $this->getPublisherValue() ?? config('app.name');
        $publishedAt = $this->getPublishedAtValue();

        $seoImage = $seo->og_image ? '/storage/' . $seo->og_image : null;
        $fallbackImage = $this->getImageValue() ? '/storage/' . $this->getImageValue() : null;
        $image = $seoImage ?? $fallbackImage;

        return new SEOData(
            title: $title,
            description: $description,
            author: $author,
            image: $image,
            url: $url,
            published_time: $publishedAt,
            section: $category,
            tags: $tags,
            schema: SchemaCollection::make()
                ->add(fn (): array => [
                    '@context' => 'https://schema.org',
                    '@type' => 'BlogPosting',
                    'headline' => $title,
                    'description' => $description,
                    'url' => $url,
                    'thumbnailUrl' => $image,
                    'articleSection' => $category,
                    'datePublished' => $publishedAt,
                    'inLanguage' => 'en',
                    'author' => [
                        [
                            '@type' => 'Organization',
                            'name' => $publisher,
                        ],
                        [
                            '@type' => 'Person',
                            'name' => $author,
                        ],
                    ],
                ])
                ->addArticle(fn (ArticleSchema $articleSchema): ArticleSchema => $articleSchema->markup(fn (Collection $markup): Collection => $markup
                    ->put('headline', $title)
                    ->put('description', $description)
                    ->put('url', $url)
                    ->put('datePublished', $publishedAt))),
            type: 'article',
            robots: app()->isLocal() ? 'noindex, nofollow' : implode(', ', $seo->robots ?? ['index', 'follow']),
            openGraphTitle: $seo->og_title ?? $title
        );
    }
}
