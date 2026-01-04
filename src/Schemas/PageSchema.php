<?php

namespace AchyutN\LaravelSEO\Schemas;

use AchyutN\LaravelSEO\Data\ResolvedSEO;
use RalphJSmit\Laravel\SEO\SchemaCollection;

trait PageSchema
{
    protected function pageSchemaType(): string
    {
        return 'WebPage';
    }

    public function buildSchema(SchemaCollection $schema, ResolvedSEO $resolvedSEO): SchemaCollection
    {
        return $schema
            ->add(fn(): array => [
                '@context' => 'https://schema.org',
                '@type' => $resolvedSEO->pageType ?? $this->pageSchemaType(),
                'name' => $resolvedSEO->title,
                'description' => $resolvedSEO->description,
                'url' => $resolvedSEO->url,
                'inLanguage' => 'en',
                'author' => $resolvedSEO->authorArray(),
                'publisher' => $resolvedSEO->publisherArray(),
            ]);
    }
}
