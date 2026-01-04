<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Schemas;

use AchyutN\LaravelSEO\Data\ResolvedSEO;
use RalphJSmit\Laravel\SEO\SchemaCollection;

trait ProductSchema
{
    public function buildSchema(SchemaCollection $schema, ResolvedSEO $resolvedSEO): SchemaCollection
    {
        return $schema
            ->add(fn (): array => [
                '@context' => 'https://schema.org',
                '@type' => $resolvedSEO->pageType ?? $this->productSchemaType(),
                'name' => $resolvedSEO->title,
                'description' => $resolvedSEO->description,
                'url' => $resolvedSEO->url,
                'image' => $resolvedSEO->image,
                'brand' => $resolvedSEO->brandArray(),
                'sku' => $resolvedSEO->sku,
                'offers' => [
                    '@type' => 'Offer',
                    'priceCurrency' => $resolvedSEO->currency,
                    'price' => $resolvedSEO->price,
                    'availability' => sprintf(
                        'https://schema.org/%s',
                        $resolvedSEO->isAvailable ? 'InStock' : 'OutOfStock'
                    ),
                ],
            ]);
    }

    protected function productSchemaType(): string
    {
        return 'Product';
    }
}
