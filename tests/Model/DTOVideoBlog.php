<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Tests\Model;

use AchyutN\LaravelSEO\Data\SitemapVideo;
use AchyutN\LaravelSEO\Traits\InteractsWithSEO;
use Illuminate\Database\Eloquent\Model;

final class DTOVideoBlog extends Model
{
    use InteractsWithSEO;

    protected $table = 'blogs';

    protected $guarded = [];

    public function sitemapVideos(): array
    {
        return [
            SitemapVideo::make(
                thumbnailLoc: 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg',
                title: 'Test Video & Tutorial',
                description: 'Learn cooking with <ingredients>',
                playerLoc: 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                duration: 600,
                publicationDate: '2024-01-15T08:00:00+00:00',
                expirationDate: '2025-01-15T08:00:00+00:00',
                rating: 4.8,
                viewCount: 15200,
                familyFriendly: true,
                requiresSubscription: false,
                live: false
            ),
        ];
    }
}
