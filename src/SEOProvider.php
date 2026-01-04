<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO;

use AchyutN\LaravelSEO\Commands\GenerateSEO;
use AchyutN\LaravelSEO\Services\SitemapService;
use Composer\InstalledVersions;
use Illuminate\Foundation\Console\AboutCommand;
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
        $this->registerCommands();
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/seo.php', 'seo'
        );
    }

    private function generateRoutes(): void
    {
        Route::get('/sitemap.xml', fn () => app(SitemapService::class)->toXML());
        Route::get('/sitemap.txt', fn () => app(SitemapService::class)->toTXT());
    }

    private function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            AboutCommand::add(
                'SEO',
                fn () => [
                    'Version' => InstalledVersions::getPrettyVersion('achyutn/laravel-seo'),
                ]
            );

            $this->commands([
                GenerateSEO::class,
            ]);
        }
    }
}
