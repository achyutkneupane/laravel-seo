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
                ->add(fn (): array => collect()
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
                ->put('offers', $this->getPriceArray($resolvedSEO))
                ->toArray()
            );
    }

    protected function productSchemaType(): string
    {
        return 'Product';
    }

    private function getPriceArray(ResolvedSEO $resolvedSEO): array
    {
        $priceSpecifications = collect()
            ->put('@type', 'UnitPriceSpecification')
            ->put('priceCurrency', $resolvedSEO->currency)
            ->put('price', $resolvedSEO->price);

        if ($resolvedSEO->hasDiscount() && $resolvedSEO->discountPrice !== null) {
            $priceSpecifications = collect()
                ->push($priceSpecifications)
                ->push(collect()
                    ->put('@type', 'UnitPriceSpecification')
                    ->put('priceType', 'https://schema.org/StrikethroughPrice')
                    ->put('price', $resolvedSEO->discountPrice)
                    ->put('priceCurrency', $resolvedSEO->currency)
                );
        }

        return collect()
            ->put('@type', 'Offer')
            ->put(
                'availability',
                sprintf(
                    'https://schema.org/%s',
                    $resolvedSEO->isAvailable ? 'InStock' : 'OutOfStock'
                )
            )
            ->put('priceSpecification', $priceSpecifications->toArray())
            ->toArray();
    }
}
