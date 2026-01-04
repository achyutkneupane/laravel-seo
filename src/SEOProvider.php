<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO;

use AchyutN\LaravelSEO\Services\SitemapService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

final class SEOProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/seo.php' => config_path('seo.php'),
        ], 'laravel-seo');

        $this->publishesMigrations([
            __DIR__.'/../database/create_seo_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_seo_table.php'),
        ], 'laravel-seo');

        $this->generateRoutes();
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/seo.php', 'seo'
        );
    }

    private function generateRoutes(): void
    {
        Route::get('/sitemap.xml', function () {
            return app(SitemapService::class)->toXML();
        })->name('sitemap.xml');
        Route::get('/sitemap.txt', function () {
            return app(SitemapService::class)->toTXT();
        });
    }
}
