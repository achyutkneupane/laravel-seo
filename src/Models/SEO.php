<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use RalphJSmit\Laravel\SEO\Models\SEO as Model;

/**
 * @property int $id
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property array<array-key, mixed>|null $meta_keywords
 * @property string|null $og_title
 * @property string|null $og_description
 * @property string|null $og_image
 * @property string|null $og_url
 * @property string|null $canonical
 * @property array<array-key, mixed>|null $robots
 * @property string|null $author
 * @property string|null $publisher
 * @property string|null $schema
 * @property string $seoable_type
 * @property int $seoable_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model|Eloquent $seoable
 *
 * @method static Builder<static>|SEO newModelQuery()
 * @method static Builder<static>|SEO newQuery()
 * @method static Builder<static>|SEO query()
 * @method static Builder<static>|SEO whereAuthor($value)
 * @method static Builder<static>|SEO whereCanonical($value)
 * @method static Builder<static>|SEO whereCreatedAt($value)
 * @method static Builder<static>|SEO whereId($value)
 * @method static Builder<static>|SEO whereMetaDescription($value)
 * @method static Builder<static>|SEO whereMetaKeywords($value)
 * @method static Builder<static>|SEO whereMetaTitle($value)
 * @method static Builder<static>|SEO whereOgDescription($value)
 * @method static Builder<static>|SEO whereOgImage($value)
 * @method static Builder<static>|SEO whereOgTitle($value)
 * @method static Builder<static>|SEO whereOgUrl($value)
 * @method static Builder<static>|SEO wherePublisher($value)
 * @method static Builder<static>|SEO whereRobots($value)
 * @method static Builder<static>|SEO whereSchema($value)
 * @method static Builder<static>|SEO whereSeoableId($value)
 * @method static Builder<static>|SEO whereSeoableType($value)
 * @method static Builder<static>|SEO whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
final class SEO extends Model
{
    protected function casts(): array
    {
        return [
            'meta_keywords' => 'array',
            'robots' => 'array',
        ];
    }
}
