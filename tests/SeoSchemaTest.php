<?php

declare(strict_types=1);

use AchyutN\LaravelSEO\Tests\Model\RichBlog;
use Illuminate\Support\Carbon;

function richBlogSchema(): array
{
    $blog = RichBlog::create([
        'title' => 'Rich Blog',
        'url' => 'https://example.com/rich-blog',
        'description' => 'A rich blog post.',
        'image' => 'images/rich.jpg',
        'published_at' => Carbon::parse('2024-01-01 10:00:00'),
    ]);

    $data = $blog->getDynamicSEOData();

    $entities = [];

    foreach ($data->schema as $builder) {
        $entities[] = value($builder);
    }

    return ['data' => $data, 'entities' => $entities];
}

beforeEach(function (): void {
    config()->set('seo.schema.organization.enabled', true);
    config()->set('seo.schema.website.enabled', true);
    config()->set('seo.schema.organization.name', 'Acme Inc');
    config()->set('seo.schema.organization.url', 'https://example.com');
    config()->set('seo.schema.organization.logo', 'https://example.com/logo.png');
    config()->set('seo.schema.organization.same_as', ['https://x.com/acme']);
    config()->set('seo.schema.website.search_url', '/blog?search={search_term_string}');
});

it('emits a site-level organization entity', function (): void {
    ['entities' => $entities] = richBlogSchema();

    $organization = collect($entities)->firstWhere('@type', 'Organization');

    expect($organization)->not->toBeNull()
        ->and($organization['@id'])->toBe('https://example.com#organization')
        ->and($organization['name'])->toBe('Acme Inc')
        ->and($organization['logo'])->toBe('https://example.com/logo.png')
        ->and($organization['sameAs'])->toBe(['https://x.com/acme']);
});

it('emits a website entity with a search action', function (): void {
    ['entities' => $entities] = richBlogSchema();

    $website = collect($entities)->firstWhere('@type', 'WebSite');

    expect($website)->not->toBeNull()
        ->and($website['@id'])->toBe('https://example.com#website')
        ->and($website['potentialAction']['@type'])->toBe('SearchAction')
        ->and($website['publisher'])->toBe(['@id' => 'https://example.com#organization']);
});

it('emits faq, howto and speakable entities from model hooks', function (): void {
    ['entities' => $entities] = richBlogSchema();

    $faq = collect($entities)->firstWhere('@type', 'FAQPage');
    $howTo = collect($entities)->firstWhere('@type', 'HowTo');

    expect($faq['mainEntity'][0]['name'])->toBe('What is SEO?')
        ->and($faq['mainEntity'][0]['acceptedAnswer']['text'])->toBe('Search engine optimization.')
        ->and($howTo['name'])->toBe('Install the package')
        ->and($howTo['step'])->toHaveCount(2)
        ->and($howTo['step'][1]['position'])->toBe(2);

    $speakable = collect($entities)
        ->first(fn (array $entity): bool => isset($entity['speakable']) && $entity['@type'] === 'WebPage');

    expect($speakable['speakable']['@type'])->toBe('SpeakableSpecification')
        ->and($speakable['speakable']['cssSelector'])->toBe(['#intro', '.summary']);
});

it('passes locale, alternates and article body to seo data', function (): void {
    ['data' => $data] = richBlogSchema();

    expect($data->type)->toBe('article')
        ->and($data->locale)->toBe('en')
        ->and($data->articleBody)->toBe('The complete article body.')
        ->and($data->alternates)->toHaveCount(2)
        ->and($data->alternates[0]->attributes['hreflang'])->toBe('en');
});

it('omits site-level schema when disabled in config', function (): void {
    config()->set('seo.schema.organization.enabled', false);
    config()->set('seo.schema.website.enabled', false);

    ['entities' => $entities] = richBlogSchema();

    expect(collect($entities)->firstWhere('@type', 'Organization'))->toBeNull()
        ->and(collect($entities)->firstWhere('@type', 'WebSite'))->toBeNull();
});
