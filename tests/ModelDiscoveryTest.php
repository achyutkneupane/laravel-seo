<?php

declare(strict_types=1);

use AchyutN\LaravelSEO\Services\SEOService;
use AchyutN\LaravelSEO\Tests\Model\Product;
use AchyutN\LaravelSEO\Tests\Model\RichBlog;

it('discovers seo models from the configured model paths', function (): void {
    config()->set('seo.model_paths', [__DIR__.'/Model']);

    $models = app(SEOService::class)->seoModels();

    expect($models)->toContain(RichBlog::class)
        ->and($models)->toContain(Product::class);
});

it('returns no models when the configured paths do not exist', function (): void {
    config()->set('seo.model_paths', [__DIR__.'/does-not-exist']);

    expect(app(SEOService::class)->seoModels())->toBe([]);
});
