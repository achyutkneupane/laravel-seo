<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Traits;

use AchyutN\LaravelSEO\Contracts\HasMarkup;
use AchyutN\LaravelSEO\Data\Breadcrumb;
use AchyutN\LaravelSEO\Data\ResolvedSEO;
use AchyutN\LaravelSEO\Models\SEO;
use AchyutN\LaravelSEO\Services\SEOService;
use AchyutN\LaravelSEO\Support\ImageUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use RalphJSmit\Laravel\SEO\Schema\BreadcrumbListSchema;
use RalphJSmit\Laravel\SEO\SchemaCollection;
use RalphJSmit\Laravel\SEO\Support\AlternateTag;
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
            SEO::query()->updateOrCreate([
                'model_id' => $model->getKey(),
                'model_type' => $model::class,
            ], app(SEOService::class)->seoAttributesFor($model));

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

        /** @var SEO|null $seo */
        $seo = $this->seo;

        $robots = app()->isLocal()
            ? 'noindex, nofollow'
            : (is_array($seo?->robots) ? implode(', ', $seo->robots) : ($seo?->robots ?? 'index, follow'));

        return new SEOData(
            title: $resolvedSEO->title,
            description: $resolvedSEO->description,
            author: $resolvedSEO->author,
            image: $resolvedSEO->image,
            url: $resolvedSEO->url,
            published_time: $resolvedSEO->publishedAt,
            modified_time: $resolvedSEO->modifiedAt,
            articleBody: $this->getArticleBodyValue(),
            section: $resolvedSEO->category,
            tags: $resolvedSEO->tags,
            schema: $schema,
            type: method_exists($this, 'seoType') ? $this->seoType() : 'article',
            locale: method_exists($this, 'seoLocale') ? $this->seoLocale() : null,
            robots: $robots,
            openGraphTitle: $seo?->og_title ?? $resolvedSEO->title,
            alternates: $this->buildAlternates(),
        );
    }

    public function resolveSEO(): ResolvedSEO
    {
        /** @var SEO|null $seo */
        $seo = $this->seo;

        $title = $seo?->meta_title ?? $this->getTitleValue() ?? '';
        $description = $this->limitDescription($seo?->meta_description ?? $seo?->og_description ?? $this->getDescriptionValue());
        $url = $seo?->canonical ?? $seo?->og_url ?? $this->getUrlValue() ?? null;
        $category = $this->getCategoryValue() ?? 'Blog';
        $tags = $seo?->meta_keywords ?? $this->getTagsValue() ?? [];

        $author = $seo?->author ?? $this->getAuthorValue();
        $authorUrl = $this->getAuthorUrlValue() ?? null;

        $publisher = $seo?->publisher ?? $this->getPublisherValue() ?? $this->getAuthorValue();
        $publisherUrl = $this->getPublisherUrlValue() ?? $this->getAuthorUrlValue();

        $seoImage = $seo?->og_image ?? null;
        $fallbackImage = $this->getImageValue() ?? null;
        $image = $seoImage ?? $fallbackImage;

        $imageURL = ImageUrl::normalize($image);

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
            articleBody: $this->getArticleBodyValue(),
        );
    }

    protected function buildDynamicSchema(): SchemaCollection
    {
        $schema = SchemaCollection::make();

        if ((bool) config('seo.schema.organization.enabled', true)) {
            $schema->add(fn (): array => $this->organizationSchema());
        }

        if ((bool) config('seo.schema.website.enabled', true)) {
            $schema->add(fn (): array => $this->websiteSchema());
        }

        if (method_exists($this, 'seoFaqs') && $this->seoFaqs() !== []) {
            $schema->add(fn (): array => $this->faqSchema());
        }

        if (method_exists($this, 'seoHowTo') && $this->seoHowTo() !== null) {
            $schema->add(fn (): array => $this->howToSchema());
        }

        if (method_exists($this, 'seoSpeakable') && $this->seoSpeakable() !== []) {
            $schema->add(fn (): array => $this->speakableSchema());
        }

        if ($this instanceof HasMarkup) {
            return $this->buildSchema($schema);
        }

        return $schema;
    }

    /**
     * @return array<int, AlternateTag>|null
     */
    protected function buildAlternates(): ?array
    {
        if (! method_exists($this, 'seoAlternates')) {
            return null;
        }

        $alternates = [];

        foreach ($this->seoAlternates() as $alternate) {
            if (! is_array($alternate) || ! isset($alternate['hreflang'], $alternate['url'])) {
                continue;
            }

            $alternates[] = new AlternateTag((string) $alternate['hreflang'], (string) $alternate['url']);
        }

        return $alternates === [] ? null : $alternates;
    }

    protected function siteUrl(): string
    {
        $url = config('seo.schema.organization.url') ?? config('app.url');

        return mb_rtrim((string) $url, '/');
    }

    protected function limitDescription(?string $description): ?string
    {
        $limit = config('seo.description.limit');

        if (! is_int($limit) || $limit <= 0 || $description === null || mb_strlen($description) <= $limit) {
            return $description;
        }

        $truncated = mb_substr($description, 0, $limit);
        $lastSpace = mb_strrpos($truncated, ' ');

        if ($lastSpace !== false && $lastSpace > 0) {
            $truncated = mb_substr($truncated, 0, $lastSpace);
        }

        return mb_rtrim($truncated, " \t\n\r\0\x0B.,;:-");
    }

    /**
     * @return array<string, mixed>
     */
    protected function organizationSchema(): array
    {
        /** @var array<string, mixed> $organization */
        $organization = config('seo.schema.organization', []);

        /** @var array<int, mixed> $sameAs */
        $sameAs = is_array($organization['same_as'] ?? null) ? $organization['same_as'] : [];
        $sameAs = array_values(array_filter($sameAs, filled(...)));

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => $organization['type'] ?? 'Organization',
            '@id' => $this->siteUrl().'#organization',
            'name' => $organization['name'] ?? config('seo.site_name') ?? config('app.name'),
            'url' => $organization['url'] ?? $this->siteUrl(),
            'logo' => $organization['logo'] ?? null,
            'sameAs' => $sameAs === [] ? null : $sameAs,
        ], static fn (mixed $value): bool => ! in_array($value, [null, '', []], true));
    }

    /**
     * @return array<string, mixed>
     */
    protected function websiteSchema(): array
    {
        /** @var array<string, mixed> $website */
        $website = config('seo.schema.website', []);
        $searchUrl = $website['search_url'] ?? null;

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            '@id' => $this->siteUrl().'#website',
            'name' => config('seo.schema.organization.name') ?? config('seo.site_name') ?? config('app.name'),
            'url' => $this->siteUrl(),
            'publisher' => ['@id' => $this->siteUrl().'#organization'],
            'potentialAction' => $searchUrl === null ? null : [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => (string) $searchUrl,
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ], static fn (mixed $value): bool => ! in_array($value, [null, '', []], true));
    }

    /**
     * @return array<string, mixed>
     */
    protected function faqSchema(): array
    {
        $questions = [];

        foreach ($this->seoFaqs() as $faq) {
            if (! is_array($faq)) {
                continue;
            }

            $question = (string) ($faq['question'] ?? '');
            $answer = (string) ($faq['answer'] ?? '');

            if ($question === '' || $answer === '') {
                continue;
            }

            $questions[] = [
                '@type' => 'Question',
                'name' => $question,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $answer,
                ],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $questions,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function howToSchema(): array
    {
        $howTo = $this->seoHowTo();

        if (! is_array($howTo)) {
            return [];
        }

        $steps = [];

        /** @var array<int, mixed> $rawSteps */
        $rawSteps = is_array($howTo['steps'] ?? null) ? $howTo['steps'] : [];

        foreach ($rawSteps as $index => $step) {
            if (! is_array($step)) {
                continue;
            }

            $steps[] = [
                '@type' => 'HowToStep',
                'position' => $index + 1,
                'name' => (string) ($step['name'] ?? ''),
                'text' => (string) ($step['text'] ?? ''),
            ];
        }

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'HowTo',
            'name' => $howTo['name'] ?? null,
            'description' => $howTo['description'] ?? null,
            'step' => $steps === [] ? null : $steps,
        ], static fn (mixed $value): bool => ! in_array($value, [null, '', []], true));
    }

    /**
     * @return array<string, mixed>
     */
    protected function speakableSchema(): array
    {
        $selectors = array_values(array_filter($this->seoSpeakable(), is_string(...)));
        $url = $this->resolveSEO()->url;

        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            '@id' => $url,
            'url' => $url,
            'speakable' => [
                '@type' => 'SpeakableSpecification',
                'cssSelector' => $selectors,
            ],
        ];
    }
}
