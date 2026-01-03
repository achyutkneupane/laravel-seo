<?php

declare(strict_types=1);

namespace LaravelSEO;

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
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/seo.php', 'seo'
        );
    }
}
