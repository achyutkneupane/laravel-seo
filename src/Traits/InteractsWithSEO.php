<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Traits;

use AchyutN\LaravelSEO\Contracts\HasMarkup;
use AchyutN\LaravelSEO\Data\Breadcrumb;
use AchyutN\LaravelSEO\Data\ResolvedSEO;
use AchyutN\LaravelSEO\Models\SEO;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use RalphJSmit\Laravel\SEO\Schema\BreadcrumbListSchema;
use RalphJSmit\Laravel\SEO\SchemaCollection;
use RalphJSmit\Laravel\SEO\Support\SEOData;

trait InteractsWithSEO
{
    use HasColumns;

    public static function bootInteractsWithSEO(): void
    {
        /**
         * @param  Model|HasColumns  $model
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

    /**
     * Add breadcrumbs to the model for schema generation
     *
     * @return array<int, Breadcrumb>
     */
    public function breadcrumbs(): array
    {
        return [];
    }

    public function getDynamicSEOData(): SEOData
    {
        $resolvedSEO = $this->resolveSEO();

        $schema = $this->buildDynamicSchema();

        if (count($this->breadcrumbs()) > 0) {
            $schema->addBreadcrumbs(function (BreadcrumbListSchema $breadcrumbs): void {
                $breadcrumbs->breadcrumbs = collect($this->breadcrumbs())
                    ->filter(fn ($breadcrumb): bool => $breadcrumb instanceof Breadcrumb)
                    ->mapWithKeys(fn (Breadcrumb $breadcrumb): array => $breadcrumb->toArray());
            });
        }

        return new SEOData(
            title: $resolvedSEO->title,
            description: $resolvedSEO->description,
            author: $resolvedSEO->author,
            image: $resolvedSEO->image,
            url: $resolvedSEO->url,
            published_time: $resolvedSEO->publishedAt,
            modified_time: $resolvedSEO->modifiedAt,
            section: $resolvedSEO->category,
            tags: $resolvedSEO->tags,
            schema: $schema,
            type: 'article',
            robots: app()->isLocal() ? 'noindex, nofollow' : implode(', ', $seo->robots ?? ['index', 'follow']),
            openGraphTitle: $seo->og_title ?? $resolvedSEO->title,
        );
    }

    public function resolveSEO(): ResolvedSEO
    {
        /** @var SEO $seo */
        $seo = $this->seo;

        $title = $seo->meta_title ?? $this->getTitleValue() ?? '';
        $description = $seo->meta_description ?? $this->getDescriptionValue();
        $url = $seo->canonical ?? $this->getUrlValue() ?? null;
        $category = $this->getCategoryValue() ?? 'Blog';
        $tags = $seo->meta_keywords ?? $this->getTagsValue() ?? [];

        $author = $seo->author ?? $this->getAuthorValue();
        $authorUrl = $this->getAuthorUrlValue() ?? null;

        $publisher = $seo->publisher ?? $this->getPublisherValue() ?? $this->getAuthorValue();
        $publisherUrl = $this->getPublisherUrlValue() ?? $this->getAuthorUrlValue();

        $seoImage = $seo->og_image ?? null;
        $fallbackImage = $this->getImageValue() ?? null;
        $image = $seoImage ?? $fallbackImage;

        $imageURL = preg_match('/^https?:\/\//', (string) $image) ? (string) $image : ($image ? '/storage/'.$image : null);

        return new ResolvedSEO(
            model: $this,
            title: $title,
            description: $description,
            url: $url,
            category: $category,
            tags: $tags,
            author: $author,
            authorUrl: $authorUrl,
            publisher: $publisher,
            publisherUrl: $publisherUrl,
            image: $imageURL,
            publishedAt: $this->getPublishedAtValue(),
            modifiedAt: $this->getModifiedAtValue(),
            pageType: $this->getPageTypeValue(),
            brand: $this->getBrandValue(),
            price: $this->getPriceValue(),
            discountPrice: $this->getdiscountPriceValue(),
            currency: $this->getCurrencyValue(),
            isAvailable: $this->getAvailabilityValue(),
            sku: $this->getSkuValue(),
        );
    }

    protected function buildDynamicSchema(): SchemaCollection
    {
        $schema = SchemaCollection::make();

        if ($this instanceof HasMarkup) {
            return $this->buildSchema($schema);
        }

        return $schema;
    }
}
