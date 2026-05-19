## Basic Model SEO Setup
```php
namespace App\Models;

use AchyutN\LaravelSEO\Traits\InteractsWithSEO;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use InteractsWithSEO;

    // Map the database 'name' column to the SEO title
    public string $titleColumn = 'name';
    
    // Map the database 'excerpt' column to the SEO description
    public string $descriptionColumn = 'excerpt';
}
```

## Model Setup with Complex Computed Values & Breadcrumbs

```php
namespace App\Models;

use AchyutN\LaravelSEO\Traits\InteractsWithSEO;
use AchyutN\LaravelSEO\Data\Breadcrumb;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use InteractsWithSEO;

    // Complex logic for the SEO title
    public function titleValue(): ?string
    {
        return "{$this->name} - Buy Now for \${$this->price}";
    }

    // Returning an array for meta keywords/tags
    public function tagsValue(): ?array
    {
        return $this->categories->pluck('name')->toArray();
    }
    
    // Adding breadcrumbs for schema
    public function breadcrumbs(): array
    {
        return [
            new Breadcrumb('Home', url('/')),
            new Breadcrumb('Products', url('/products')),
            new Breadcrumb($this->name, $this->urlValue()),
        ];
    }
}
```

## Schema Generation Setup

```php
namespace App\Models;

use AchyutN\LaravelSEO\Contracts\HasMarkup;
use AchyutN\LaravelSEO\Traits\InteractsWithSEO;
use AchyutN\LaravelSEO\Schemas\BlogSchema;
use Illuminate\Database\Eloquent\Model;

class Article extends Model implements HasMarkup
{
    use InteractsWithSEO;
    use BlogSchema; // Provides BlogPosting and Article Schema.org markup

    public string $authorColumn = 'author_name';
    public string $publishedAtColumn = 'published_at';
}
```

## Writing Custom Schema

```php
<?php

declare(strict_types=1);

namespace App\Schemas;

use App\Models\Author;
use RalphJSmit\Laravel\SEO\SchemaCollection;

trait AuthorSchema
{
    public function buildSchema(SchemaCollection $schema): SchemaCollection
    {
        $resolvedSEO = $this->resolveSEO();

        /** @var Author $model */
        $model = $resolvedSEO->getModel();

        return $schema
            ->add(fn (): array => [
                '@context' => 'https://schema.org',
                '@type' => $this->blogSchemaType(),
                '@id' => $resolvedSEO->url,
                'mainEntityOfPage' => $resolvedSEO->url,
                'name' => $resolvedSEO->title,
                'description' => $resolvedSEO->description,
                'url' => $resolvedSEO->url,
                /** @phpstan-var string|null $email */
                'email' => $model->getAttribute('email'),
                'image' => $resolvedSEO->image,
                'sameAs' => array_values($model->social_links),
                'interactionStatistic' => [
                    [
                        '@type' => 'InteractionCounter',
                        'interactionType' => 'http://schema.org/UserPageVisits',
                        'userInteractionCount' => $model->total_views,
                    ],
                    [
                        '@type' => 'InteractionCounter',
                        'interactionType' => 'http://schema.org/PlusOnes',
                        'userInteractionCount' => $model->getTotalVotes(),
                    ],
                ],
            ]);
    }

    protected function blogSchemaType(): string
    {
        return 'Person';
    }
}
```
