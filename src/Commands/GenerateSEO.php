<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Commands;

use AchyutN\LaravelSEO\Models\SEO;
use AchyutN\LaravelSEO\Services\SitemapService;
use AchyutN\LaravelSEO\Traits\InteractsWithSEO;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use ReflectionClass;

final class GenerateSEO extends Command
{
    protected $signature = 'seo:generate {--regenerate : Regenerate SEO entries even if they exist}';

    protected $description = 'Generate missing SEO entries for all models that uses InteractsWithSEO trait';

    public function __construct(
        public SitemapService $sitemapService
    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        /** @var bool $regenerate */
        $regenerate = $this->option('regenerate') ?? false;

        $models = $this->discoverModels();

        if ($models === []) {
            $this->info('No models using InteractsWithSEO found.');

            return;
        }

        foreach ($models as $modelClass) {
            /** @phpstan-var string $modelClass */
            $this->info("Processing model: {$modelClass}");

            /** @var Model $model */
            $model = app($modelClass);

            $model->query()
                ->chunkById(100, function ($records) use ($regenerate): void {
                    foreach ($records as $instance) {
                        /** @var SEO|null $seo */
                        $seo = $instance->getAttribute('seo');

                        if (! $regenerate && $seo !== null) {
                            continue;
                        }

                        [
                            'title' => $title,
                            'description' => $description,
                            'tags' => $tags,
                            'author' => $author,
                            'publisher' => $publisher,
                        ] = $this->sitemapService->getModelValues($instance);

                        SEO::query()
                            ->updateOrCreate([
                                'model_id' => $instance->getKey(),
                                'model_type' => $instance::class,
                            ], [
                                'meta_title' => $title,
                                'og_title' => $title,
                                'meta_description' => $description,
                                'og_description' => $description,
                                'meta_keywords' => $tags,
                                'author' => $author,
                                'publisher' => $publisher,
                                'robots' => ['index', 'follow'],
                            ]);
                    }
                });
        }
    }

    /** @return array<int, class-string<Model>> */
    protected function discoverModels(): array
    {
        $path = app_path('Models');

        $models = [];

        if (! is_dir($path)) {
            return $models;
        }

        foreach (File::allFiles($path) as $file) {
            $class = $this->classFromFile($file->getPathname());
            if (! $class) {
                continue;
            }
            if (! class_exists($class)) {
                continue;
            }

            $reflection = new ReflectionClass($class);
            if ($reflection->isAbstract()) {
                continue;
            }
            if (! is_subclass_of($class, Model::class)) {
                continue;
            }

            if (in_array(
                InteractsWithSEO::class,
                class_uses_recursive($class),
                true
            )) {
                $models[] = $class;
            }
        }

        return array_unique($models);
    }

    protected function classFromFile(string $path): ?string
    {
        $contents = file_get_contents($path);

        if ($contents === false) {
            return null;
        }

        if (
            ! preg_match('/namespace\s+(.+?);/', $contents, $ns) ||
            ! preg_match('/class\s+(\w+)/', $contents, $cls)
        ) {
            return null;
        }

        return $ns[1].'\\'.$cls[1];
    }
}
