<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Schemas;

use AchyutN\LaravelSEO\Data\ResolvedSEO;
use Illuminate\Support\Collection;
use RalphJSmit\Laravel\SEO\Schema\ArticleSchema;
use RalphJSmit\Laravel\SEO\SchemaCollection;

trait BlogSchema
{
    public function buildSchema(SchemaCollection $schema, ResolvedSEO $resolvedSEO): SchemaCollection
    {
        return $schema
            ->add(fn (): array => [
                '@context' => 'https://schema.org',
                '@type' => $resolvedSEO->pageType ?? $this->blogSchemaType(),
                'headline' => $resolvedSEO->title,
                'description' => $resolvedSEO->description,
                'url' => $resolvedSEO->url,
                'thumbnailUrl' => $resolvedSEO->image,
                'articleSection' => $resolvedSEO->category,
                'datePublished' => $resolvedSEO->publishedAt,
                'inLanguage' => 'en',
                'author' => $resolvedSEO->authorAndPublisher(),
            ])
            ->addArticle(fn (ArticleSchema $articleSchema): ArticleSchema => $articleSchema->markup(fn (Collection $markup): Collection => $markup
                ->put('headline', $resolvedSEO->title)
                ->put('description', $resolvedSEO->description)
                ->put('url', $resolvedSEO->url)
                ->put('thumbnailUrl', $resolvedSEO->image)
                ->put('author', $resolvedSEO->authorAndPublisher())
                ->put('datePublished', $resolvedSEO->publishedAt)
            ));
    }

    protected function blogSchemaType(): string
    {
        return 'BlogPosting';
    }
}
