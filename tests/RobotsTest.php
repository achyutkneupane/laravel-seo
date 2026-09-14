<?php

declare(strict_types=1);

use AchyutN\LaravelSEO\Http\Controllers\RobotsController;
use Illuminate\Support\Facades\Route;

it('generates a robots.txt with allow, disallow and sitemap directives', function (): void {
    config()->set('seo.robots_txt.user_agent', '*');
    config()->set('seo.robots_txt.allow', ['/']);
    config()->set('seo.robots_txt.disallow', ['/admin', '/pulse']);

    $response = app(RobotsController::class)();

    expect($response->getStatusCode())->toBe(200)
        ->and($response->headers->get('Content-Type'))->toBe('text/plain; charset=UTF-8')
        ->and($response->getContent())->toContain('User-agent: *')
        ->and($response->getContent())->toContain('Allow: /')
        ->and($response->getContent())->toContain('Disallow: /admin')
        ->and($response->getContent())->toContain('Sitemap: http://localhost/sitemap.xml');
});

it('does not register the robots route by default', function (): void {
    expect(Route::has('laravel-seo.robots'))->toBeFalse();
});
