<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Tests;

use AchyutN\LaravelSEO\SEOProvider;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as BaseTestCase;
use RalphJSmit\Laravel\SEO\LaravelSEOServiceProvider;

abstract class TestCase extends BaseTestCase
{
    use LazilyRefreshDatabase;
    use WithWorkbench;

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->setUpDatabase();
    }

    protected function getPackageProviders($app): array
    {
        return [
            SEOProvider::class,
            LaravelSEOServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        config()->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        config()->set('database.default', 'testing');
        config()->set('seo.database', 'testing');
        config()->set('app.url', 'http://localhost');
        config()->set('app.key', 'base64:Hupx3yAySikrM2/edkZQNQHslgDWYfiBfCuSThJ5SK8=');
    }

    protected function setUpDatabase(): void
    {
        $schema = Schema::connection('testing');

        $schema->create('blogs', function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->string('title');
            $blueprint->string('url')->nullable();
            $blueprint->text('description');
            $blueprint->string('image')->nullable();
            $blueprint->json('tags')->nullable();
            $blueprint->timestamp('published_at');
            $blueprint->timestamps();
        });

        $schema->create('seo', function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->morphs('model');
            $blueprint->string('meta_title')->nullable();
            $blueprint->text('meta_description')->nullable();
            $blueprint->string('meta_keywords')->nullable();
            $blueprint->string('og_title')->nullable();
            $blueprint->text('og_description')->nullable();
            $blueprint->string('og_image')->nullable();
            $blueprint->string('og_url')->nullable();
            $blueprint->string('canonical')->nullable();
            $blueprint->string('robots')->nullable();
            $blueprint->string('author')->nullable();
            $blueprint->string('publisher')->nullable();
            $blueprint->timestamps();
        });
    }
}
