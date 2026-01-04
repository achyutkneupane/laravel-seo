<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Commands;

use AchyutN\LaravelSEO\Models\SEO;
use AchyutN\LaravelSEO\Traits\InteractsWithSEO;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use ReflectionClass;

class GenerateSEO extends Command
{
    protected $signature = 'seo:generate {--regenerate : Regenerate SEO entries even if they exist}';
    protected $description = 'Generate missing SEO entries for all models that uses InteractsWithSEO trait';

    public function handle(): void
    {
        /** @var bool $regenerate */
        $regenerate = $this->option('regenerate') ?? false;

        $models = $this->discoverModels();

        if (empty($models)) {
            $this->info('No models using InteractsWithSEO found.');
            return;
        }

        foreach ($models as $modelClass) {
            $this->info("Processing model: {$modelClass}");

            $modelClass::query()->chunkById(100, function ($records) use ($regenerate) {
                foreach ($records as $instance) {
                    if (!$regenerate && $instance->seo) {
                        continue;
                    }

                    SEO::query()
                        ->updateOrCreate([
                            'model_id' => $instance->getKey(),
                            'model_type' => $instance::class,
                        ], [
                            'meta_title' => $instance->getTitleValue(),
                            'og_title' => $instance->getTitleValue(),
                            'meta_description' => $instance->getDescriptionValue(),
                            'og_description' => $instance->getDescriptionValue(),
                            'meta_keywords' => $instance->getTagsValue(),
                            'author' => $instance->getAuthorValue(),
                            'publisher' => $instance->getPublisherValue(),
                            'robots' => ['index', 'follow'],
                        ]);
                }
            });
        }
    }

    protected function discoverModels(): array
    {
        $path = app_path('Models');

        $models = [];

        if (!is_dir($path)) {
            return $models;
        }

        foreach (File::allFiles($path) as $file) {
            $class = $this->classFromFile($file->getPathname());

            if (!$class || !class_exists($class)) {
                continue;
            }

            $reflection = new ReflectionClass($class);

            if (
                $reflection->isAbstract() ||
                !is_subclass_of($class, Model::class)
            ) {
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

        if (
            !preg_match('/namespace\s+(.+?);/', $contents, $ns) ||
            !preg_match('/class\s+(\w+)/', $contents, $cls)
        ) {
            return null;
        }

        return $ns[1] . '\\' . $cls[1];
    }
}
