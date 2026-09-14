<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

it('registers the sitemap routes as controllers so route caching works', function (): void {
    $xml = Route::getRoutes()->getByName('laravel-seo.sitemap');
    $txt = Route::getRoutes()->getByName('laravel-seo.sitemap.txt');

    expect($xml)->not->toBeNull()
        ->and($txt)->not->toBeNull()
        ->and($xml->getAction('uses'))->not->toBeInstanceOf(Closure::class)
        ->and($txt->getAction('uses'))->not->toBeInstanceOf(Closure::class);
});

it('serves the xml and txt sitemaps over http', function (): void {
    $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
    $this->get('/sitemap.txt')->assertOk()->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
});
