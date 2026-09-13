<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Schemas;

use AchyutN\LaravelSEO\Contracts\HasMarkup;
use RalphJSmit\Laravel\SEO\SchemaCollection;

trait BlogSchema
{
    public function buildSchema(SchemaCollection $schema): SchemaCollection
    {
        /** @var HasMarkup $this */
        $resolvedSEO = $this->resolveSEO();

        return $schema
            ->add(
                fn (): array => array_filter([
                    '@context' => 'https://schema.org',
                    '@type' => $resolvedSEO->pageType ?? $this->blogSchemaType(),
                    'headline' => $resolvedSEO->title,
                    'description' => $resolvedSEO->description,
                    'url' => $resolvedSEO->url,
                    '@id' => $resolvedSEO->url,
                    'mainEntityOfPage' => $resolvedSEO->url === null ? null : [
                        '@type' => 'WebPage',
                        '@id' => $resolvedSEO->url,
                    ],
                    'image' => $resolvedSEO->image,
                    'articleSection' => $resolvedSEO->category,
                    'keywords' => $resolvedSEO->tags === [] ? null : implode(', ', $resolvedSEO->tags),
                    'inLanguage' => app()->getLocale(),
                    'datePublished' => $resolvedSEO->publishedAt?->toIso8601String(),
                    'dateModified' => $resolvedSEO->modifiedAt?->toIso8601String(),
                    'articleBody' => $resolvedSEO->articleBody,
                    'author' => $resolvedSEO->authorArray(),
                    'publisher' => $resolvedSEO->publisherArray(),
                ], static fn (mixed $value): bool => ! in_array($value, [null, '', []], true))
            );
    }

    protected function blogSchemaType(): string
    {
        return 'BlogPosting';
    }
}
