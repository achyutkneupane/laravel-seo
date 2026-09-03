<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Tests\Model;

use AchyutN\LaravelSEO\Traits\InteractsWithSEO;
use Illuminate\Database\Eloquent\Model;

final class ArrayVideoBlog extends Model
{
    use InteractsWithSEO;

    protected $table = 'blogs';

    protected $guarded = [];

    public function sitemapVideos(): array
    {
        return [
            [
                'thumbnail_loc' => 'https://example.com/thumb.jpg',
                'title' => 'Raw Array Video',
                'description' => 'A video defined as array',
                'content_loc' => 'https://example.com/video.mp4',
                'duration' => 300,
                'family_friendly' => false,
            ],
        ];
    }
}
