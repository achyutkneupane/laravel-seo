# Laravel SEO

![Packagist Version](https://img.shields.io/packagist/v/achyutn/laravel-seo?label=Latest%20Version)
![Packagist Downloads](https://img.shields.io/packagist/dt/achyutn/laravel-seo?label=Packagist%20Downloads)
![Packagist Stars](https://img.shields.io/packagist/stars/achyutn/laravel-seo?label=Stars)
[![Lint & Test PR](https://github.com/achyutkneupane/laravel-seo/actions/workflows/prlint.yml/badge.svg)](https://github.com/achyutkneupane/laravel-seo/actions/workflows/prlint.yml)

An opinionated Laravel SEO package to manage SEO on Eloquent models.
This package makes use of an un-opinionated package [ralphjsmit/laravel-seo](https://github.com/ralphjsmit/laravel-seo) and adds a pattern of auto-generating SEO metadata and sitemaps for Eloquent models with customization.

This package lets you generate SEO metadata and sitemap directly from Eloquent models without
manually wiring SEO data in controllers or views.
It supports multiple schema types including [Blog](#blog-schema), [Product](#product-schema), and generic [Page](#page-schema) schema.

## Installation

You can install the package via composer:

```bash
composer require achyutn/laravel-seo
```

You must publish package config and migration stubs to your application using the following command:

```bash
php artisan vendor:publish --tag="laravel-seo"
```

This generates a `config/seo.php` configuration file and a migration file
for the `seo` table in your database.

> `ralphjsmit/laravel-seo` will also be installed as a dependency. Publishing the configuration
> and migration of that package will break the functionality of this package.  
> If you want full customization, I recommend you to check out that package directly from
> its [GitHub repository](https://github.com/ralphjsmit/laravel-seo).

Next, run the migration to create the `seo` table:

```bash
php artisan migrate
```

## Usage

To use the package, simply add the [`AchyutN\LaravelSEO\Traits\InteractsWithSEO`](src/Traits/InteractsWithSEO.php) trait to any Eloquent model
you want to manage SEO for.

```php
use AchyutN\LaravelSEO\Traits\InteractsWithSEO;

class Post extends Model
{
    use InteractsWithSEO;

    // ...
}
```

This will automatically use the default columns (title, description, etc).
You can also auto-generate [breadcrumbs](#breadcrumbs) and [schema markup](#schema-types) for your model by implementing the respective methods or traits.

### Generate SEO Entries

After adding the trait to your model, you need to generate SEO entries for existing records.
You can do this by running the following Artisan command:

```bash
php artisan seo:generate
```

It also supports re-generating SEO entries for models that already have SEO data.
You can use the `--regenerate` flag to update existing entries:

```bash
php artisan seo:generate --regenerate
```

### Schema Types

This package supports multiple schema types using traits:

- [BlogSchema](#blog-schema) for blog posts
- [ProductSchema](#product-schema) for products (e-commerce)
- [PageSchema](#page-schema) for generic pages

Each schema trait implements the `buildSchema(SchemaCollection $schema): SchemaCollection` method required by the `AchyutN\LaravelSEO\Contracts\HasMarkup` interface and resolves SEO data from your model internally. To use any schema, add the corresponding trait to your model along with the interface `AchyutN\LaravelSEO\Contracts\HasMarkup`.

#### Blog Schema

To use the Blog schema, add the `AchyutN\LaravelSEO\Schemas\BlogSchema` trait to your model:

```php
use AchyutN\LaravelSEO\Contracts\HasMarkup;
use AchyutN\LaravelSEO\Schemas\BlogSchema;

class Post extends Model implements HasMarkup
{
    use InteractsWithSEO;
    use BlogSchema;

    // ...
}
```

#### Product Schema

To use the Product schema, add the `AchyutN\LaravelSEO\Schemas\ProductSchema` trait to your model:

```php
use AchyutN\LaravelSEO\Contracts\HasMarkup;
use AchyutN\LaravelSEO\Schemas\ProductSchema;

class Product extends Model implements HasMarkup
{
    use InteractsWithSEO;
    use ProductSchema;

    // ...
}
```

#### Page Schema

To use the Page schema, add the `AchyutN\LaravelSEO\Schemas\PageSchema` trait to your model:

```php
use AchyutN\LaravelSEO\Contracts\HasMarkup;
use AchyutN\LaravelSEO\Schemas\PageSchema;

class Page extends Model implements HasMarkup
{
    use InteractsWithSEO;
    use PageSchema;

    // ...
}
```

### Breadcrumbs

You can also manage breadcrumbs by defining a `breadcrumbs()` method on your model that returns an array of [`Breadcrumb`](src/Data/Breadcrumb.php) items.

```php
use AchyutN\LaravelSEO\Data\Breadcrumb;

class Post extends Model
{
    use InteractsWithSEO;

    public function breadcrumbs(): array
    {
        return [
            new Breadcrumb(label: 'Home', url: route('home')),
            new Breadcrumb(label: 'Blog', url: route('blog.index')),
            new Breadcrumb(label: $this->title, url: route('blog.show', $this)),
        ];
    }
}
```

### Rendering SEO Tags

To render the SEO tags in your views, you can use the following Blade directive:

```blade
{!! seo($model) !!}
```

Replace `$model` with the instance of your Eloquent model.

### Sitemaps

Once you have models using the `InteractsWithSEO` trait, you automatically have them included in your sitemap.
You can access the sitemap in two formats:

- XML Sitemap: `/sitemap.xml`
- TXT Sitemap: `/sitemap.txt`

The XML sitemap will be auto-injected in your blade layout along with the metadata.

### Sitemap Images

By default, the XML sitemap includes a single image per URL using the model's `imageValue()` or `$imageColumn`.
To include multiple images for a URL, define a `sitemapImages(): array` method on your model. You can return:

- An array of string URLs or storage paths: `['https://example.com/images/1.jpg', 'images/2.jpg']`
- An array of strongly-typed [`SitemapImage`](src/Data/SitemapImage.php) objects: `[SitemapImage::make(url: 'https://...', title: '...', caption: '...')]`
- An array of associative arrays: `[['loc' => 'https://...', 'title' => '...']]`

When multiple plain string images are provided, only the `<image:loc>` tag is output for each image, following Google's recommended practice. If custom titles, captions, geo-locations, or licenses are provided via `SitemapImage` DTOs or associative arrays, they are rendered accordingly.

```php
use AchyutN\LaravelSEO\Data\SitemapImage;

class Post extends Model
{
    use InteractsWithSEO;

    public function sitemapImages(): array
    {
        return [
            SitemapImage::make(
                url: 'https://example.com/images/post-1.jpg',
                title: 'Featured Image',
                caption: 'Post preview image'
            ),
            'https://example.com/images/post-2.jpg',
        ];
    }
}
```

### Sitemap Videos

To include video information (such as YouTube embeds or direct MP4 files) in your XML sitemap, define a `sitemapVideos(): array` method on your model.
You can return either [`SitemapVideo`](src/Data/SitemapVideo.php) DTOs or raw associative arrays.

Each video entry must include `thumbnail_loc`, `title`, `description`, and either `player_loc` (e.g. YouTube embed URL) or `content_loc` (direct media file URL).

```php
use AchyutN\LaravelSEO\Data\SitemapVideo;

class Post extends Model
{
    use InteractsWithSEO;

    public function sitemapVideos(): array
    {
        return [
            SitemapVideo::make(
                thumbnailLoc: 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg',
                title: 'How to cook pasta',
                description: 'A step-by-step guide to cooking perfect pasta.',
                playerLoc: 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                duration: 600,
                publicationDate: '2024-01-15T08:00:00+00:00',
                familyFriendly: true,
            ),
        ];
    }
}
```

Supported video fields:

| Field | Required | Type | Description |
|-------|----------|------|-------------|
| `thumbnail_loc` | Yes | `string` | URL to the video thumbnail image |
| `title` | Yes | `string` | Title of the video (max 100 chars) |
| `description` | Yes | `string` | Description of the video (max 2048 chars) |
| `player_loc` | Yes* | `string` | URL to video player / embed (e.g. YouTube embed URL) |
| `content_loc` | Yes* | `string` | Direct URL to the video file (.mp4, .mov, etc.) |
| `duration` | No | `int` | Duration in seconds (1 - 28800) |
| `publication_date` | No | `string` | ISO 8601 publication date |
| `expiration_date` | No | `string` | ISO 8601 expiration date |
| `rating` | No | `float` | Video rating (0.0 - 5.0) |
| `view_count` | No | `int` | Number of views |
| `family_friendly` | No | `bool` | Whether the video is family-friendly |
| `requires_subscription` | No | `bool` | Whether a subscription is required |
| `live` | No | `bool` | Whether the video is a live stream |

\* At least one of `player_loc` or `content_loc` is required by Google. Entries missing both are skipped, and `SitemapVideo` DTOs throw an `InvalidArgumentException`.

Google's length and range limits are enforced: `title` and `description` are truncated to 100 and 2048 characters respectively, and out-of-range `duration`, `rating`, and `view_count` values are omitted from raw array entries (`SitemapVideo` DTOs reject them).

### Site-level Schema (GEO)

The package can emit site-level `Organization` and `WebSite` JSON-LD on every page so AI search engines can identify and cite your brand. It is opt-in; enable it once in `config/seo.php`:

```php
'schema' => [
    'organization' => [
        'enabled' => true,
        'name' => config('app.name'),
        'url' => config('app.url'),
        'logo' => 'https://example.com/logo.png',
        'same_as' => [
            'https://x.com/acme',
            'https://www.linkedin.com/company/acme',
        ],
    ],
    'website' => [
        'enabled' => true,
        'search_url' => '/blog?search={search_term_string}',
    ],
],
```

Set `enabled` to `true` to activate it, or leave it `false` if your application already emits these entities (for example through a `SEOManager` transformer).

### Answer Engine Schema (AEO)

Define any of the following optional methods on your model and the matching schema is added automatically:

```php
/** @return array<int, array{question: string, answer: string}> */
public function seoFaqs(): array
{
    return [
        ['question' => 'What is Laravel SEO?', 'answer' => 'A package that generates metadata and schema from models.'],
    ];
}

/** @return array{name: string, description: string, steps: array<int, array{name: string, text: string}>} */
public function seoHowTo(): array
{
    return [
        'name' => 'Install the package',
        'description' => 'Install the package and generate SEO metadata.',
        'steps' => [
            ['name' => 'Require', 'text' => 'Run composer require achyutn/laravel-seo.'],
            ['name' => 'Backfill', 'text' => 'Run php artisan seo:generate.'],
        ],
    ];
}

/** @return array<int, string> */
public function seoSpeakable(): array
{
    return ['#summary', '.article-body'];
}
```

- `seoFaqs()` emits `FAQPage` schema (featured snippets / People Also Ask).
- `seoHowTo()` emits `HowTo` schema.
- `seoSpeakable()` emits `SpeakableSpecification` for voice assistants.

### Internationalisation and Article Depth

```php
public function seoLocale(): string
{
    return app()->getLocale();
}

/** @return array<int, array{hreflang: string, url: string}> */
public function seoAlternates(): array
{
    return [
        ['hreflang' => 'en', 'url' => url('/en/blog/'.$this->getKey())],
        ['hreflang' => 'fr', 'url' => url('/fr/blog/'.$this->getKey())],
    ];
}

public function seoArticleBody(): string
{
    return strip_tags((string) $this->content);
}
```

`seoAlternates()` is emitted both as `<link rel="alternate" hreflang>` tags and as `<xhtml:link>` entries in the XML sitemap. `seoArticleBody()` feeds the `articleBody` field so AI engines can cite the full text.

### Managed robots.txt, Model Discovery and Description Limits

`config/seo.php` exposes a few operational extras:

```php
// Directories scanned by `php artisan seo:generate` for InteractsWithSEO models.
'model_paths' => [
    app_path('Models'),
],

// Serve a managed /robots.txt that always includes a Sitemap directive.
'robots_txt' => [
    'enabled' => true,
    'user_agent' => '*',
    'allow' => ['/'],
    'disallow' => ['/admin', '/pulse'],
],

// Optionally trim long meta descriptions on a word boundary.
'description' => [
    'limit' => 160,
],
```

The `robots_txt` endpoint is disabled by default so it never conflicts with an application-defined robots file. Enable it on greenfield projects.

### Customization

This package resolves SEO data using a simple priority order:

1. A custom `*Value()` method, if defined on the model
2. A custom `$*Column` property, if defined on the model
3. A default column name

You can customize each field independently.

#### Resolution Priority

For each field:

1. `*Value()` method
2. `$*Column` property
3. Default column name

Methods always take precedence over properties.

#### Supported Override Methods

Define these **on your model** when the value is computed or derived. Organized by context:

##### Common Methods

| Method                | Return Type     | When to use               |
|-----------------------|-----------------|---------------------------|
| `titleValue()`        | `?string`       | Computed or dynamic title |
| `descriptionValue()`  | `?string`       | Generated description     |
| `categoryValue()`     | `?string`       | Derived category          |
| `imageValue()`        | `?string`       | Media URL                 |
| `authorValue()`       | `?string`       | Relation-based author     |
| `authorUrlValue()`    | `?string`       | Author profile link       |
| `publisherValue()`    | `?string`       | Brand or company name     |
| `publisherUrlValue()` | `?string`       | Publisher homepage        |
| `tagsValue()`         | `array<string>` | Normalized tags           |
| `urlValue()`          | `?string`       | The page URL              |
| `publishedAtValue()`  | `?Carbon`       | Computed publish date     |
| `modifiedAtValue()`   | `?Carbon`       | Computed updated date     |
| `pageTypeValue()`     | `?string`       | Custom page type          |

##### Product-Specific Methods

| Method                  | Return Type | When to use          |
|-------------------------|-------------|----------------------|
| `brandValue()`          | `?string`   | Product brand        |
| `priceValue()`          | `?float`    | Product price        |
| `discountPriceValue()`  | `?float`    | Discounted price     |
| `currencyValue()`       | `?string`   | Price currency       |
| `availabilityValue()`   | `bool`      | Product availability |
| `skuValue()`            | `?string`   | Product SKU          |

##### Page-Specific Methods

| Method             | Return Type | When to use                         |
|--------------------|-------------|-------------------------------------|
| `pageTypeValue()`  | `?string`   | Type of page (landing, about, etc.) |

##### Example

```php
class Product extends Model
{
    use InteractsWithSEO;

    protected function priceValue(): ?float
    {
        return $this->price;
    }

    protected function availabilityValue(): bool
    {
        return $this->is_available;
    }
}
```

#### Supported Properties

Define these **on your Eloquent model** to change which column is used. Organized by context:

##### Common Properties

| Property              | Default         | Purpose             |
|-----------------------|-----------------|---------------------|
| `$titleColumn`        | `title`         | Page title          |
| `$descriptionColumn`  | `description`   | Meta description    |
| `$categoryColumn`     | `category`      | Content category    |
| `$imageColumn`        | `image`         | Open Graph image    |
| `$authorColumn`       | `author`        | Author name         |
| `$authorUrlColumn`    | `author_url`    | Author profile URL  |
| `$publisherColumn`    | `publisher`     | Publisher name      |
| `$publisherUrlColumn` | `publisher_url` | Publisher URL       |
| `$tagsColumn`         | `tags`          | Tags or keywords    |
| `$urlColumn`          | `url`           | The page URL        |
| `$publishedAtColumn`  | `created_at`    | Publish date        |
| `$modifiedAtColumn`   | `updated_at`    | Updated date        |
| `$pageTypeColumn`     | `page_type`     | Page type           |

##### Product-Specific Properties

| Property               | Default           | Purpose              |
|------------------------|-------------------|----------------------|
| `$brandColumn`         | `brand`           | Product brand        |
| `$priceColumn`         | `price`           | Product price        |
| `$discountPriceColumn` | `discount_price`  | Discounted price     |
| `$currencyColumn`      | `currency`        | Price currency       |
| `$availabilityColumn`  | `is_available`    | Product availability |
| `$skuColumn`           | `sku`             | Product SKU          |

##### Example

```php
class Product extends Model
{
    use InteractsWithSEO;

    protected string $priceColumn = 'product_price';
    protected string $brandColumn = 'product_brand';
}
```

#### Notes

- All overrides are optional
- Do not define both a property and a method unless intentional
- IDEs and static analyzers understand all extension points via PHPDoc

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).

## Contributing

Contributions are welcome! Please create a pull request or open an issue if you find any bugs or have feature requests.

## Support

If you find this package useful, please consider starring the repository on GitHub to show your support.
