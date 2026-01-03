<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use LazilyRefreshDatabase;
    use WithWorkbench;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    protected function defineEnvironment($app): void
    {
        config()->set('database.connections.the_test', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        config()->set('database.default', 'the_test');
        config()->set('app.key', 'base64:Hupx3yAySikrM2/edkZQNQHslgDWYfiBfCuSThJ5SK8=');
    }

    protected function setUpDatabase(): void
    {
        app('db')
            ->connection()
            ->getSchemaBuilder()
            ->create('blogs', function (Blueprint $blueprint): void {
                $blueprint->id();
                $blueprint->string('title');
                $blueprint->text('description');
                $blueprint->string('image')->nullable();
                $blueprint->json('tags')->nullable();
                $blueprint->timestamp('published_at');
                $blueprint->timestamps();
            });
    }
}
