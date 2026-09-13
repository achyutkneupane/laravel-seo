<?php

declare(strict_types=1);

use AchyutN\LaravelSEO\Tests\Model\NoPriceProduct;
use AchyutN\LaravelSEO\Tests\Model\Product;
use AchyutN\LaravelSEO\Tests\Model\SchemaBlog;
use Illuminate\Support\Carbon;

/**
 * @return array<int, mixed>
 */
function schemaTraitEntities(object $model): array
{
    $entities = [];

    foreach ($model->getDynamicSEOData()->schema as $builder) {
        $entities[] = value($builder);
    }

    return $entities;
}

beforeEach(function (): void {
    config()->set('seo.schema.organization.enabled', false);
    config()->set('seo.schema.website.enabled', false);
});

it('emits a single blog posting entity with separate author and publisher', function (): void {
    $blog = SchemaBlog::create([
        'title' => 'Schema Blog',
        'url' => 'https://example.com/blog/schema',
        'description' => 'A schema blog.',
        'published_at' => Carbon::parse('2024-01-01 10:00:00'),
    ]);

    $entities = schemaTraitEntities($blog);

    $articles = array_values(array_filter(
        $entities,
        static fn (mixed $entity): bool => is_array($entity)
            && in_array($entity['@type'] ?? null, ['BlogPosting', 'Article'], true)
    ));

    expect($articles)->toHaveCount(1);

    $article = $articles[0];

    expect($article['@type'])->toBe('BlogPosting')
        ->and($article['author'][0])->toMatchArray(['@type' => 'Person', 'name' => 'Jane Doe'])
        ->and($article['publisher'][0])->toMatchArray(['@type' => 'Organization', 'name' => 'Acme Inc'])
        ->and($article['articleBody'])->toBe('The complete article body.')
        ->and($article['mainEntityOfPage']['@id'])->toBe('https://example.com/blog/schema');
});

it('emits a valid product offer with discount metadata', function (): void {
    $product = Product::create([
        'title' => 'Test Product',
        'url' => 'https://example.com/products/test',
        'description' => 'A product.',
        'published_at' => Carbon::now(),
    ]);

    $entities = schemaTraitEntities($product);

    $productEntity = collect($entities)->firstWhere('@type', 'Product');

    expect($productEntity)->not->toBeNull()
        ->and($productEntity['sku'])->toBe('SKU-001')
        ->and($productEntity['brand'])->toMatchArray(['@type' => 'Brand', 'name' => 'Acme'])
        ->and($productEntity['offers']['price'])->toBe(80.0)
        ->and($productEntity['offers']['priceCurrency'])->toBe('USD')
        ->and($productEntity['offers']['availability'])->toBe('https://schema.org/InStock')
        ->and($productEntity['offers']['priceSpecification']['priceType'])->toBe('https://schema.org/StrikethroughPrice')
        ->and($productEntity['offers']['priceSpecification']['price'])->toBe(100.0);
});

it('omits product offers when no price is available', function (): void {
    $product = NoPriceProduct::create([
        'title' => 'Free Product',
        'url' => 'https://example.com/products/free',
        'description' => 'A free product.',
        'published_at' => Carbon::now(),
    ]);

    $entities = schemaTraitEntities($product);
    $productEntity = collect($entities)->firstWhere('@type', 'Product');

    expect($productEntity)->not->toBeNull()
        ->and($productEntity)->not->toHaveKey('offers');
});
