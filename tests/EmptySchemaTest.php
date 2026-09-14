<?php

declare(strict_types=1);

use AchyutN\LaravelSEO\Tests\Model\MalformedHookBlog;
use Illuminate\Support\Carbon;

/**
 * @return array<int, mixed>
 */
function malformedEntities(): array
{
    $blog = MalformedHookBlog::create([
        'title' => 'Malformed Hooks',
        'url' => 'https://example.com/blog/malformed',
        'description' => 'A post with malformed schema hooks.',
        'published_at' => Carbon::parse('2024-01-01 10:00:00'),
    ]);

    $entities = [];

    foreach ($blog->getDynamicSEOData()->schema as $builder) {
        $entities[] = value($builder);
    }

    return $entities;
}

beforeEach(function (): void {
    config()->set('seo.schema.organization.enabled', false);
    config()->set('seo.schema.website.enabled', false);
});

it('omits the faq page when no valid questions remain', function (): void {
    expect(collect(malformedEntities())->firstWhere('@type', 'FAQPage'))->toBeNull();
});

it('omits the howto when no valid steps remain', function (): void {
    expect(collect(malformedEntities())->firstWhere('@type', 'HowTo'))->toBeNull();
});

it('omits the speakable webpage when no valid selectors remain', function (): void {
    $speakable = collect(malformedEntities())
        ->first(fn (mixed $entity): bool => is_array($entity) && isset($entity['speakable']));

    expect($speakable)->toBeNull();
});
