<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Tests\Model;

use AchyutN\LaravelSEO\Traits\InteractsWithSEO;
use Illuminate\Database\Eloquent\Model;

final class LongVideoBlog extends Model
{
    use InteractsWithSEO;

    protected $table = 'blogs';

    protected $guarded = [];

    /** @return array<int, array<string, mixed>> */
    public function sitemapVideos(): array
    {
        return [
            [
                'thumbnail_loc' => 'https://example.com/thumb.jpg',
                'title' => str_repeat('T', 150),
                'description' => str_repeat('D', 2100),
                'player_loc' => 'https://www.youtube.com/embed/long-video',
                'duration' => 0,
                'rating' => 9.9,
                'view_count' => -5,
            ],
        ];
    }
}
