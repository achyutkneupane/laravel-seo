<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Schemas;

use AchyutN\LaravelSEO\Contracts\HasMarkup;
use AchyutN\LaravelSEO\Data\ResolvedSEO;
use Illuminate\Support\Collection;
use RalphJSmit\Laravel\SEO\Schema\ArticleSchema;
use RalphJSmit\Laravel\SEO\SchemaCollection;

trait BlogSchema
{
    public function buildSchema(SchemaCollection $schema): SchemaCollection
    {
        /** @var HasMarkup $this */
        $resolvedSEO = $this->resolveSEO();

        return $schema
            ->add(
                fn () => collect()
                    ->put('@context', 'https://schema.org')
                    ->put('@type', $resolvedSEO->pageType ?? $this->blogSchemaType())
                    ->put('headline', $resolvedSEO->title)
                    ->put('inLanguage', 'en')
                    ->when(
                        $resolvedSEO->description,
                        fn (Collection $collection) => $collection->put('description', $resolvedSEO->description)
                    )
                    ->when(
                        $resolvedSEO->url,
                        fn (Collection $collection) => $collection->put('url', $resolvedSEO->url)
                            ->put('@id', $resolvedSEO->url)
                    )
                    ->when(
                        $resolvedSEO->image,
                        fn (Collection $collection) => $collection->put('thumbnailUrl', $resolvedSEO->image)
                    )
                    ->when(
                        $resolvedSEO->category,
                        fn (Collection $collection) => $collection->put('articleSection', $resolvedSEO->category)
                    )
                    ->when(
                        $resolvedSEO->publishedAt,
                        fn (Collection $collection) => $collection->put('datePublished', $resolvedSEO->publishedAt)
                    )
                    ->put('author', $resolvedSEO->authorAndPublisher())
            )
            ->addArticle(
                function (ArticleSchema $articleSchema) use ($resolvedSEO): ArticleSchema {
                    return $articleSchema->markup(
                        fn (Collection $markup): Collection => $markup
                            ->put('headline', $resolvedSEO->title)
                            ->put('description', $resolvedSEO->description)
                            ->put('url', $resolvedSEO->url)
                            ->put('thumbnailUrl', $resolvedSEO->image)
                            ->put('articleSection', $resolvedSEO->category)
                            ->put('author', $resolvedSEO->authorAndPublisher())
                            ->put('datePublished', $resolvedSEO->publishedAt)
                    );
                }
            );
    }

    protected function blogSchemaType(): string
    {
        return 'BlogPosting';
    }
}
