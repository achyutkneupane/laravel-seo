<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Commands;

use Illuminate\Console\Command;

class RegenerateSEO extends Command
{
    protected $signature = 'seo:regenerate';
    protected $description = 'Regenerate SEO data for all models that uses InteractsWithSEO trait';

    public function handle(): void
    {
        // Implementation to regenerate SEO data
    }
}
