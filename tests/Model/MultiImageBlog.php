<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Tests\Model;

use AchyutN\LaravelSEO\Traits\InteractsWithSEO;
use Illuminate\Database\Eloquent\Model;

final class MultiImageBlog extends Model
{
    use InteractsWithSEO;

    protected $table = 'blogs';

    protected $guarded = [];

    public function sitemapImages(): array
    {
        return [
            'https://example.com/images/gallery-1.jpg',
            'images/gallery-2.jpg',
        ];
    }
}
