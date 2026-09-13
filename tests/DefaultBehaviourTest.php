<?php

declare(strict_types=1);

use AchyutN\LaravelSEO\Models\SEO;
use AchyutN\LaravelSEO\Tests\Model\Blog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

function defaultBlog(): Blog
{
    return Blog::create([
        'title' => 'Default Behaviour Post',
        'url' => 'https://example.com/blog/default-behaviour',
        'description' => 'A post without an SEO row.',
        'published_at' => Carbon::parse('2024-01-01 10:00:00'),
    ]);
}

it('keeps the seo relation null when no record exists', function (): void {
    $blog = defaultBlog();
    SEO::query()->delete();

    $fresh = Blog::query()->findOrFail($blog->getKey());

    expect($fresh->seo)->toBeNull();
});

it('resolves seo safely without an existing record', function (): void {
    $blog = defaultBlog();
    SEO::query()->delete();

    $fresh = Blog::query()->findOrFail($blog->getKey());

    expect($fresh->resolveSEO()->title)->toBe('Default Behaviour Post')
        ->and($fresh->resolveSEO()->url)->toBe('https://example.com/blog/default-behaviour');
});

it('disables site level schema by default', function (): void {
    expect(config('seo.schema.organization.enabled'))->toBeFalse()
        ->and(config('seo.schema.website.enabled'))->toBeFalse();
});

it('keeps the default sitemap route paths', function (): void {
    expect(Route::getRoutes()->getByName('laravel-seo.sitemap')?->uri())->toBe('sitemap.xml')
        ->and(Route::getRoutes()->getByName('laravel-seo.sitemap.txt')?->uri())->toBe('sitemap.txt');
});
