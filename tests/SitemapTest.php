<?php

declare(strict_types=1);

use AchyutN\LaravelSEO\Data\SitemapVideo;
use AchyutN\LaravelSEO\Services\SitemapService;
use AchyutN\LaravelSEO\Tests\Model\ArrayVideoBlog;
use AchyutN\LaravelSEO\Tests\Model\Blog;
use AchyutN\LaravelSEO\Tests\Model\DTOImageBlog;
use AchyutN\LaravelSEO\Tests\Model\DTOVideoBlog;
use AchyutN\LaravelSEO\Tests\Model\InvalidVideoBlog;
use AchyutN\LaravelSEO\Tests\Model\LongVideoBlog;
use AchyutN\LaravelSEO\Tests\Model\MultiImageBlog;
use Illuminate\Support\Carbon;

it('generates xml sitemap with default single image, title and description', function (): void {
    Blog::create([
        'title' => 'First Blog Post',
        'url' => 'https://example.com/blog/first',
        'description' => 'This is a description of the first blog.',
        'image' => 'images/featured.jpg',
        'published_at' => Carbon::parse('2024-01-01 10:00:00'),
    ]);

    /** @var SitemapService $sitemapService */
    $sitemapService = app(SitemapService::class);
    $response = $sitemapService->toXML();

    expect($response->getStatusCode())->toBe(200);
    expect($response->headers->get('Content-Type'))->toBe('application/xml; charset=UTF-8');

    $content = $response->getContent();
    expect($content)->toContain('xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"');
    expect($content)->toContain('xmlns:video="http://www.google.com/schemas/sitemap-video/1.1"');
    expect($content)->toContain('<loc>https://example.com/blog/first</loc>');
    expect($content)->toContain('<image:loc>http://localhost/storage/images/featured.jpg</image:loc>');
    expect($content)->toContain('<image:title>First Blog Post</image:title>');
    expect($content)->toContain('<image:caption>This is a description of the first blog.</image:caption>');
});

it('generates xml sitemap with multiple image strings without caption or title', function (): void {
    MultiImageBlog::create([
        'title' => 'Multi Image Post',
        'url' => 'https://example.com/blog/multi-image',
        'description' => 'A post with multiple images',
        'published_at' => Carbon::now(),
    ]);

    /** @var SitemapService $sitemapService */
    $sitemapService = app(SitemapService::class);
    $content = $sitemapService->toXML()->getContent();

    expect($content)->toContain('<loc>https://example.com/blog/multi-image</loc>');
    expect($content)->toContain('<image:loc>https://example.com/images/gallery-1.jpg</image:loc>');
    expect($content)->toContain('<image:loc>http://localhost/storage/images/gallery-2.jpg</image:loc>');
    expect($content)->not()->toContain('<image:title>');
    expect($content)->not()->toContain('<image:caption>');
});

it('generates xml sitemap with SitemapImage DTOs including custom titles and captions', function (): void {
    DTOImageBlog::create([
        'title' => 'DTO Image Post',
        'url' => 'https://example.com/blog/dto-image',
        'description' => 'Testing DTO images',
        'published_at' => Carbon::now(),
    ]);

    /** @var SitemapService $sitemapService */
    $sitemapService = app(SitemapService::class);
    $content = $sitemapService->toXML()->getContent();

    expect($content)->toContain('<loc>https://example.com/blog/dto-image</loc>');
    expect($content)->toContain('<image:loc>https://example.com/images/img1.jpg</image:loc>');
    expect($content)->toContain('<image:title>Custom Title 1</image:title>');
    expect($content)->toContain('<image:caption>Custom Caption 1</image:caption>');
    expect($content)->toContain('<image:geo_location>Kathmandu, Nepal</image:geo_location>');
    expect($content)->toContain('<image:license>https://example.com/license</image:license>');
});

it('generates xml sitemap with SitemapVideo DTO and full metadata', function (): void {
    DTOVideoBlog::create([
        'title' => 'Video Post',
        'url' => 'https://example.com/blog/video-dto',
        'description' => 'Post with video',
        'published_at' => Carbon::now(),
    ]);

    /** @var SitemapService $sitemapService */
    $sitemapService = app(SitemapService::class);
    $content = $sitemapService->toXML()->getContent();

    expect($content)->toContain('<loc>https://example.com/blog/video-dto</loc>');
    expect($content)->toContain('<video:video>');
    expect($content)->toContain('<video:thumbnail_loc>https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg</video:thumbnail_loc>');
    expect($content)->toContain('<video:title>Test Video &amp; Tutorial</video:title>');
    expect($content)->toContain('<video:description>Learn cooking with &lt;ingredients&gt;</video:description>');
    expect($content)->toContain('<video:player_loc>https://www.youtube.com/embed/dQw4w9WgXcQ</video:player_loc>');
    expect($content)->toContain('<video:duration>600</video:duration>');
    expect($content)->toContain('<video:publication_date>2024-01-15T08:00:00+00:00</video:publication_date>');
    expect($content)->toContain('<video:expiration_date>2025-01-15T08:00:00+00:00</video:expiration_date>');
    expect($content)->toContain('<video:rating>4.8</video:rating>');
    expect($content)->toContain('<video:view_count>15200</video:view_count>');
    expect($content)->toContain('<video:family_friendly>yes</video:family_friendly>');
    expect($content)->toContain('<video:requires_subscription>no</video:requires_subscription>');
    expect($content)->toContain('<video:live>no</video:live>');
});

it('generates xml sitemap with raw associative video arrays for backward compatibility', function (): void {
    ArrayVideoBlog::create([
        'title' => 'Array Video Post',
        'url' => 'https://example.com/blog/video-array',
        'description' => 'Testing array video',
        'published_at' => Carbon::now(),
    ]);

    /** @var SitemapService $sitemapService */
    $sitemapService = app(SitemapService::class);
    $content = $sitemapService->toXML()->getContent();

    expect($content)->toContain('<loc>https://example.com/blog/video-array</loc>');
    expect($content)->toContain('<video:video>');
    expect($content)->toContain('<video:thumbnail_loc>https://example.com/thumb.jpg</video:thumbnail_loc>');
    expect($content)->toContain('<video:title>Raw Array Video</video:title>');
    expect($content)->toContain('<video:description>A video defined as array</video:description>');
    expect($content)->toContain('<video:content_loc>https://example.com/video.mp4</video:content_loc>');
    expect($content)->toContain('<video:duration>300</video:duration>');
    expect($content)->toContain('<video:family_friendly>no</video:family_friendly>');
});

it('generates plain text sitemap', function (): void {
    Blog::create([
        'title' => 'TXT Blog Post',
        'url' => 'https://example.com/blog/txt-post',
        'description' => 'Testing TXT sitemap',
        'published_at' => Carbon::now(),
    ]);

    /** @var SitemapService $sitemapService */
    $sitemapService = app(SitemapService::class);
    $response = $sitemapService->toTXT();

    expect($response->getStatusCode())->toBe(200);
    expect($response->headers->get('Content-Type'))->toBe('text/plain; charset=UTF-8');
    expect($response->getContent())->toContain('https://example.com/blog/txt-post');
});

it('sanitizes invalid xml control characters from output', function (): void {
    Blog::create([
        'title' => "Title With\x00Null\x08Byte",
        'url' => 'https://example.com/blog/control-chars',
        'description' => "Description with \x1F control char",
        'image' => 'images/sample.jpg',
        'published_at' => Carbon::now(),
    ]);

    /** @var SitemapService $sitemapService */
    $sitemapService = app(SitemapService::class);
    $content = $sitemapService->toXML()->getContent();

    expect($content)->toContain('<image:title>Title WithNullByte</image:title>');
    expect($content)->toContain('<image:caption>Description with  control char</image:caption>');
    expect($content)->not()->toContain("\x00");
    expect($content)->not()->toContain("\x08");
    expect($content)->not()->toContain("\x1F");
});

it('sanitizes crlf injection attempts in plain text sitemaps', function (): void {
    Blog::create([
        'title' => 'CRLF Blog Post',
        'url' => "https://example.com/blog/injected\r\nhttps://malicious.com",
        'description' => 'Testing CRLF prevention',
        'published_at' => Carbon::now(),
    ]);

    /** @var SitemapService $sitemapService */
    $sitemapService = app(SitemapService::class);
    $response = $sitemapService->toTXT();

    expect($response->getContent())->toContain('https://example.com/blog/injectedhttps://malicious.com');
    expect(explode("\n", (string) $response->getContent()))->toHaveCount(1);
});

it('correctly normalizes paths with leading slashes and storage prefix', function (): void {
    Blog::create([
        'title' => 'Normalized Path Post',
        'url' => 'https://example.com/blog/storage-path',
        'description' => 'Testing storage paths',
        'image' => '/storage/uploads/photo.jpg',
        'published_at' => Carbon::now(),
    ]);

    /** @var SitemapService $sitemapService */
    $sitemapService = app(SitemapService::class);
    $content = $sitemapService->toXML()->getContent();

    expect($content)->toContain('<image:loc>http://localhost/storage/uploads/photo.jpg</image:loc>');
    expect($content)->not()->toContain('storage//');
    expect($content)->not()->toContain('storage/storage/');
});

it('generates dynamic seo data without undefined variable errors', function (): void {
    $blog = Blog::create([
        'title' => 'Dynamic SEO Post',
        'url' => 'https://example.com/blog/dynamic',
        'description' => 'Testing dynamic SEO data',
        'published_at' => Carbon::now(),
    ]);

    $seoData = $blog->getDynamicSEOData();

    expect($seoData->title)->toBe('Dynamic SEO Post');
    expect($seoData->description)->toBe('Testing dynamic SEO data');
    expect($seoData->url)->toBe('https://example.com/blog/dynamic');
});

it('skips videos without player_loc or content_loc', function (): void {
    InvalidVideoBlog::create([
        'title' => 'Invalid Video Post',
        'url' => 'https://example.com/blog/invalid-video',
        'description' => 'Testing invalid video entries',
        'published_at' => Carbon::now(),
    ]);

    /** @var SitemapService $sitemapService */
    $sitemapService = app(SitemapService::class);
    $content = $sitemapService->toXML()->getContent();

    expect($content)->not()->toContain('Video Without Location');
    expect($content)->toContain('<video:title>Valid Video</video:title>');
    expect($content)->toContain('<video:player_loc>https://www.youtube.com/embed/abc123</video:player_loc>');
});

it('throws when a sitemap video dto has no player or content location', function (): void {
    expect(fn (): SitemapVideo => SitemapVideo::make(
        thumbnailLoc: 'https://example.com/thumb.jpg',
        title: 'No Location',
        description: 'Missing player and content locations',
    ))->toThrow(InvalidArgumentException::class, 'A video sitemap entry requires either player_loc or content_loc.');
});

it('truncates video title and description to google limits', function (): void {
    LongVideoBlog::create([
        'title' => 'Long Video Post',
        'url' => 'https://example.com/blog/long-video',
        'description' => 'Testing video truncation',
        'published_at' => Carbon::now(),
    ]);

    /** @var SitemapService $sitemapService */
    $sitemapService = app(SitemapService::class);
    $content = $sitemapService->toXML()->getContent();

    expect($content)->toContain('<video:title>'.str_repeat('T', 100).'</video:title>');
    expect($content)->not()->toContain(str_repeat('T', 101));
    expect($content)->toContain('<video:description>'.str_repeat('D', 2048).'</video:description>');
    expect($content)->not()->toContain(str_repeat('D', 2049));
});

it('omits out of range metadata from raw video arrays', function (): void {
    LongVideoBlog::create([
        'title' => 'Out Of Range Video Post',
        'url' => 'https://example.com/blog/out-of-range-video',
        'description' => 'Testing out of range metadata',
        'published_at' => Carbon::now(),
    ]);

    /** @var SitemapService $sitemapService */
    $sitemapService = app(SitemapService::class);
    $content = $sitemapService->toXML()->getContent();

    expect($content)->not()->toContain('<video:duration>');
    expect($content)->not()->toContain('<video:rating>');
    expect($content)->not()->toContain('<video:view_count>');
});

it('throws for out of range metadata in sitemap video dtos', function (): void {
    $base = [
        'thumbnailLoc' => 'https://example.com/thumb.jpg',
        'title' => 'Invalid Metadata',
        'description' => 'Testing invalid metadata',
        'playerLoc' => 'https://www.youtube.com/embed/abc123',
    ];

    expect(fn (): SitemapVideo => SitemapVideo::make(...[...$base, 'duration' => 0]))
        ->toThrow(InvalidArgumentException::class, 'Video duration must be between 1 and 28800 seconds.');

    expect(fn (): SitemapVideo => SitemapVideo::make(...[...$base, 'rating' => 5.5]))
        ->toThrow(InvalidArgumentException::class, 'Video rating must be between 0.0 and 5.0.');

    expect(fn (): SitemapVideo => SitemapVideo::make(...[...$base, 'viewCount' => -1]))
        ->toThrow(InvalidArgumentException::class, 'Video view count cannot be negative.');
});
