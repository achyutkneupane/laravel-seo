<?php

declare(strict_types=1);

use AchyutN\LaravelSEO\Models\SEO;

return [
    'model' => SEO::class,
    'sitemap' => '/sitemap.xml',
    'sitemap_txt' => '/sitemap.txt',
    'database' => config('database.default', 'mysql'),

    /*
     * Directories scanned by the `seo:generate` command for models that use
     * the InteractsWithSEO trait. Add your own model directories here.
     */
    'model_paths' => [
        app_path('Models'),
    ],

    /*
     * A managed robots.txt endpoint. Disabled by default so it never conflicts
     * with an application-defined robots.txt; enable it on greenfield projects.
     */
    'robots_txt' => [
        'enabled' => false,
        'user_agent' => '*',
        'allow' => ['/'],
        'disallow' => [],
    ],

    /*
     * Optional hard limit for the meta description. When set, longer
     * descriptions are trimmed on a word boundary.
     */
    'description' => [
        'limit' => null,
    ],

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
