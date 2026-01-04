<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Contracts;

use AchyutN\LaravelSEO\Data\ResolvedSEO;
use RalphJSmit\Laravel\SEO\SchemaCollection;

interface HasMarkup
{
    public function buildSchema(
        SchemaCollection $schema,
        ResolvedSEO $seo
    ): SchemaCollection;
}
