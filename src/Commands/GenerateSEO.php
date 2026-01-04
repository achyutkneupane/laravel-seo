<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Commands;

use AchyutN\LaravelSEO\Models\SEO;
use AchyutN\LaravelSEO\Services\SEOService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;

final class GenerateSEO extends Command
{
    protected $signature = 'seo:generate {--regenerate : Regenerate SEO entries even if they exist}';

    protected $description = 'Generate missing SEO entries for all models that uses InteractsWithSEO trait';

    public function __construct(
        public SEOService $service
    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        /** @var bool $regenerate */
        $regenerate = $this->option('regenerate') ?? false;

        $models = $this->service->seoModels();

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
                        ] = $this->service->getModelValues($instance);

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
}
