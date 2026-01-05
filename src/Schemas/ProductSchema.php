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
                'offers' => $this->getPriceArray($resolvedSEO),
            ]);
    }

    protected function productSchemaType(): string
    {
        return 'Product';
    }

    private function getPriceArray(ResolvedSEO $resolvedSEO): array
    {
        $priceSpecifications = [
            [
                '@type' => 'UnitPriceSpecification',
                'priceCurrency' => $resolvedSEO->currency,
                'price' => $resolvedSEO->price,
            ],
        ];

        if ($resolvedSEO->hasDiscount() && $resolvedSEO->discountPrice !== null) {
            $priceSpecifications[] = [
                '@type' => 'UnitPriceSpecification',
                'priceType' => 'https://schema.org/StrikethroughPrice',
                'price' => $resolvedSEO->discountPrice,
                'priceCurrency' => $resolvedSEO->currency,
            ];
        }

        return [
            '@type' => 'Offer',
            'availability' => sprintf(
                'https://schema.org/%s',
                $resolvedSEO->isAvailable ? 'InStock' : 'OutOfStock'
            ),
            'priceSpecification' => $priceSpecifications,
        ];
    }
}
