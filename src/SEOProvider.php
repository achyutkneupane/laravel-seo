<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO;

use AchyutN\LaravelSEO\Commands\GenerateSEO;
use AchyutN\LaravelSEO\Http\Controllers\SitemapController;
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
        Route::get($this->routePath(config('seo.sitemap'), '/sitemap.xml'), [SitemapController::class, 'xml'])
            ->name('laravel-seo.sitemap');

        Route::get($this->routePath(config('seo.sitemap_txt'), '/sitemap.txt'), [SitemapController::class, 'txt'])
            ->name('laravel-seo.sitemap.txt');
    }

    private function routePath(mixed $configured, string $default): string
    {
        if (! is_string($configured) || ! str_starts_with($configured, '/')) {
            return $default;
        }

        return $configured;
    }

    private function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            AboutCommand::add(
                'SEO',
                fn (): array => [
                    'Version' => InstalledVersions::getPrettyVersion('achyutn/laravel-seo'),
                ]
            );

            $this->commands([
                GenerateSEO::class,
            ]);
        }
    }
}
