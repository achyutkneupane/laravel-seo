<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Tests\Model;

use AchyutN\LaravelSEO\Data\SitemapImage;
use AchyutN\LaravelSEO\Traits\InteractsWithSEO;
use Illuminate\Database\Eloquent\Model;

final class DTOImageBlog extends Model
{
    use InteractsWithSEO;

    protected $table = 'blogs';

    protected $guarded = [];

    public function sitemapImages(): array
    {
        return [
            SitemapImage::make(
                url: 'https://example.com/images/img1.jpg',
                title: 'Custom Title 1',
                caption: 'Custom Caption 1',
                geoLocation: 'Kathmandu, Nepal',
                license: 'https://example.com/license'
            ),
        ];
    }
}
