<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Tests\Model;

use AchyutN\LaravelSEO\Traits\InteractsWithSEO;
use Illuminate\Database\Eloquent\Model;

final class InvalidVideoBlog extends Model
{
    use InteractsWithSEO;

    protected $table = 'blogs';

    protected $guarded = [];

    public function sitemapVideos(): array
    {
        return [
            [
                'thumbnail_loc' => 'https://example.com/thumb.jpg',
                'title' => 'Video Without Location',
                'description' => 'This entry has neither player_loc nor content_loc',
            ],
            [
                'thumbnail_loc' => 'https://example.com/thumb-2.jpg',
                'title' => 'Valid Video',
                'description' => 'This entry has a player location',
                'player_loc' => 'https://www.youtube.com/embed/abc123',
            ],
        ];
    }
}
