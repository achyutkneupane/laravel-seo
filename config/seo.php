<?php

declare(strict_types=1);

use AchyutN\LaravelSEO\Models\SEO;

return [
    'model' => SEO::class,
    'sitemap' => '/sitemap.xml',
    'sitemap_txt' => '/sitemap.txt',
    'database' => config('database.default', 'mysql'),

    /*
     * Site-level structured data that is emitted on every page. Providing these
     * values gives AI and answer engines a stable brand entity to cite.
     */
    'schema' => [
        'organization' => [
            'enabled' => true,
            'type' => 'Organization',
            'name' => config('app.name'),
            'url' => config('app.url'),
            'logo' => null,
            'same_as' => [],
        ],
        'website' => [
            'enabled' => true,
            // A search URL template, e.g. '/blog?search={search_term_string}'.
            'search_url' => null,
        ],
    ],
];
