<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Schemas;

use AchyutN\LaravelSEO\Contracts\HasMarkup;
use AchyutN\LaravelSEO\Data\ResolvedSEO;
use RalphJSmit\Laravel\SEO\SchemaCollection;

trait PageSchema
{
    public function buildSchema(SchemaCollection $schema): SchemaCollection
    {
        /** @var HasMarkup $this */
        $resolvedSEO = $this->resolveSEO();

        return $schema
        ->add(fn (): array => collect()
                ->put('@context', 'https://schema.org')
                ->put('@type', $resolvedSEO->pageType ?? $this->pageSchemaType())
                ->when(
                    $resolvedSEO->title,
                    fn ($collection) => $collection->put('name', $resolvedSEO->title)
                )
                ->when(
                    $resolvedSEO->description,
                    fn ($collection) => $collection->put('description', $resolvedSEO->description)
                )
                ->when(
                    $resolvedSEO->url,
                    fn ($collection) => $collection->put('url', $resolvedSEO->url)
                        ->put('@id', $resolvedSEO->url)
                )
                ->put('inLanguage', 'en')
                ->when(
                    $resolvedSEO->authorArray(),
                    fn ($collection) => $collection->put('author', $resolvedSEO->authorArray())
                )
                ->when(
                    $resolvedSEO->publisherArray(),
                    fn ($collection) => $collection->put('publisher', $resolvedSEO->publisherArray())
                )
                ->toArray()
            );
    }

    protected function pageSchemaType(): string
    {
        return 'WebPage';
    }
}
