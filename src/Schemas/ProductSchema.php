<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Schemas;

use AchyutN\LaravelSEO\Contracts\HasMarkup;
use AchyutN\LaravelSEO\Data\ResolvedSEO;
use RalphJSmit\Laravel\SEO\SchemaCollection;

trait ProductSchema
{
    public function buildSchema(SchemaCollection $schema): SchemaCollection
    {
        /** @var HasMarkup $this */
        $resolvedSEO = $this->resolveSEO();

        return $schema
            ->add(
                fn () => collect()
                    ->put('@context', 'https://schema.org')
                    ->put('@type', $resolvedSEO->pageType ?? $this->productSchemaType())
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
                    ->when(
                        $resolvedSEO->image,
                        fn ($collection) => $collection->put('image', $resolvedSEO->image)
                    )
                    ->when(
                        $resolvedSEO->brand,
                        fn ($collection) => $collection->put('brand', $resolvedSEO->brandArray())
                    )
                    ->when(
                        $resolvedSEO->sku,
                        fn ($collection) => $collection->put('sku', $resolvedSEO->sku)
                    )
                    ->when(
                        $resolvedSEO->price !== null,
                        fn ($collection) => $collection->put('offers', $this->getPriceArray($resolvedSEO))
                    )
            );
    }

    protected function productSchemaType(): string
    {
        return 'Product';
    }

    private function getPriceArray(ResolvedSEO $resolvedSEO): array
    {
        $price = $resolvedSEO->hasDiscount() ? $resolvedSEO->discountPrice : $resolvedSEO->price;

        $priceSpecification = $resolvedSEO->hasDiscount()
            ? array_filter([
                '@type' => 'UnitPriceSpecification',
                'priceType' => 'https://schema.org/StrikethroughPrice',
                'price' => $resolvedSEO->price,
                'priceCurrency' => $resolvedSEO->currency,
            ], static fn (mixed $value): bool => ! in_array($value, [null, ''], true))
            : null;

        return array_filter([
            '@type' => 'Offer',
            'price' => $price,
            'priceCurrency' => $resolvedSEO->currency,
            'availability' => sprintf(
                'https://schema.org/%s',
                $resolvedSEO->isAvailable ? 'InStock' : 'OutOfStock'
            ),
            'priceSpecification' => $priceSpecification,
        ], static fn (mixed $value): bool => ! in_array($value, [null, '', []], true));
    }
}
