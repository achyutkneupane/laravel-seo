<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Contracts;

use AchyutN\LaravelSEO\Data\ResolvedSEO;
use RalphJSmit\Laravel\SEO\SchemaCollection;

/**
 * @template TKey of array-key
 */
interface HasMarkup
{
    /**
     * @param  SchemaCollection<TKey>  $schema
     * @return SchemaCollection<TKey>
     */
    public function buildSchema(
        SchemaCollection $schema,
        ResolvedSEO $seo
    ): SchemaCollection;
}
