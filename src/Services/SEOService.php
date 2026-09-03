<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Services;

use AchyutN\LaravelSEO\Data\SitemapImage;
use AchyutN\LaravelSEO\Data\SitemapVideo;
use AchyutN\LaravelSEO\Traits\InteractsWithSEO;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use ReflectionClass;

final class SEOService
{
    /**
     * @return array{
     *     url: string|null,
     *     imageUrl: string|null,
     *     title: string|null,
     *     description: string|null,
     *     updatedAt: Carbon|null,
     *     tags: array<int, string>,
     *     author: string|null,
     *     publisher: string|null,
     *     sitemapImages: array<int, array{loc: string, title: string|null, caption: string|null, geo_location?: string|null, license?: string|null}>,
     *     sitemapVideos: array<int, array{thumbnail_loc: string, title: string, description: string, player_loc?: string, content_loc?: string, duration?: int, publication_date?: string, expiration_date?: string, rating?: float, view_count?: int, family_friendly?: bool, requires_subscription?: bool, live?: bool}>
     * }
     */
    public function getModelValues(Model $model): array
    {
        /** @var string|null $url */
        $url = method_exists($model, 'getUrlValue')
            ? $model->getUrlValue()
            : (method_exists($model, 'getURLValue') ? $model->getURLValue() : null);
        /** @var string|null $imagePath */
        $imagePath = method_exists($model, 'getImageValue') ? $model->getImageValue() : null;
        /** @var string|null $title */
        $title = method_exists($model, 'getTitleValue') ? $model->getTitleValue() : null;
        /** @var string|null $description */
        $description = method_exists($model, 'getDescriptionValue') ? $model->getDescriptionValue() : null;
        /** @var Carbon $updatedAt */
        $updatedAt = method_exists($model, 'getModifiedAtValue') ? $model->getModifiedAtValue() : null;
        /** @var array<int, string> $tags */
        $tags = method_exists($model, 'getTagsValue') ? $model->getTagsValue() : [];
        /** @var string|null $author */
        $author = method_exists($model, 'getAuthorValue') ? $model->getAuthorValue() : null;
        /** @var string|null $publisher */
        $publisher = method_exists($model, 'getPublisherValue') ? $model->getPublisherValue() : null;
        /** @var array<int, string|SitemapImage|array<string, mixed>> $sitemapImages */
        $sitemapImages = method_exists($model, 'getSitemapImagesValue') ? $model->getSitemapImagesValue() : [];
        /** @var array<int, SitemapVideo|array<string, mixed>> $sitemapVideos */
        $sitemapVideos = method_exists($model, 'getSitemapVideosValue') ? $model->getSitemapVideosValue() : [];

        $imageURL = $this->normalizeImageUrl($imagePath);

        /** @var array<int, array{loc: string, title: string|null, caption: string|null, geo_location?: string|null, license?: string|null}> $processedSitemapImages */
        $processedSitemapImages = [];
        foreach ($sitemapImages as $sitemapImage) {
            $rawUrl = null;
            $imgTitle = null;
            $imgCaption = null;
            $geoLocation = null;
            $license = null;

            if ($sitemapImage instanceof SitemapImage) {
                $rawUrl = $sitemapImage->getUrl();
                $imgTitle = $sitemapImage->getTitle();
                $imgCaption = $sitemapImage->getCaption();
                $geoLocation = $sitemapImage->getGeoLocation();
                $license = $sitemapImage->getLicense();
            } elseif (is_array($sitemapImage)) {
                $locVal = $sitemapImage['loc'] ?? $sitemapImage['url'] ?? null;
                $rawUrl = is_string($locVal) ? $locVal : null;
                $titleVal = $sitemapImage['title'] ?? null;
                $imgTitle = is_string($titleVal) ? $titleVal : null;
                $captionVal = $sitemapImage['caption'] ?? null;
                $imgCaption = is_string($captionVal) ? $captionVal : null;
                $geoVal = $sitemapImage['geo_location'] ?? null;
                $geoLocation = is_string($geoVal) ? $geoVal : null;
                $licenseVal = $sitemapImage['license'] ?? null;
                $license = is_string($licenseVal) ? $licenseVal : null;
            } elseif (is_string($sitemapImage)) {
                $rawUrl = $sitemapImage;
            }

            if (! filled($rawUrl)) {
                continue;
            }

            $processedImageUrl = $this->normalizeImageUrl($rawUrl);

            if ($processedImageUrl !== null) {
                $imageData = [
                    'loc' => $processedImageUrl,
                    'title' => $imgTitle,
                    'caption' => $imgCaption,
                ];

                if ($geoLocation !== null) {
                    $imageData['geo_location'] = $geoLocation;
                }

                if ($license !== null) {
                    $imageData['license'] = $license;
                }

                $processedSitemapImages[] = $imageData;
            }
        }

        /** @var array<int, array{thumbnail_loc: string, title: string, description: string, player_loc?: string, content_loc?: string, duration?: int, publication_date?: string, expiration_date?: string, rating?: float, view_count?: int, family_friendly?: bool, requires_subscription?: bool, live?: bool}> $processedSitemapVideos */
        $processedSitemapVideos = [];
        foreach ($sitemapVideos as $video) {
            if ($video instanceof SitemapVideo) {
                $processedSitemapVideos[] = $video->toArray();
            } elseif (
                isset($video['thumbnail_loc'], $video['title'], $video['description'])
                && is_string($video['thumbnail_loc'])
                && is_string($video['title'])
                && is_string($video['description'])
            ) {
                $videoData = [
                    'thumbnail_loc' => $video['thumbnail_loc'],
                    'title' => $video['title'],
                    'description' => $video['description'],
                ];

                if (isset($video['player_loc']) && is_string($video['player_loc'])) {
                    $videoData['player_loc'] = $video['player_loc'];
                }

                if (isset($video['content_loc']) && is_string($video['content_loc'])) {
                    $videoData['content_loc'] = $video['content_loc'];
                }

                if (isset($video['duration']) && (is_int($video['duration']) || is_numeric($video['duration']))) {
                    $videoData['duration'] = (int) $video['duration'];
                }

                if (isset($video['publication_date']) && is_string($video['publication_date'])) {
                    $videoData['publication_date'] = $video['publication_date'];
                }

                if (isset($video['expiration_date']) && is_string($video['expiration_date'])) {
                    $videoData['expiration_date'] = $video['expiration_date'];
                }

                if (isset($video['rating']) && (is_float($video['rating']) || is_numeric($video['rating']))) {
                    $videoData['rating'] = (float) $video['rating'];
                }

                if (isset($video['view_count']) && (is_int($video['view_count']) || is_numeric($video['view_count']))) {
                    $videoData['view_count'] = (int) $video['view_count'];
                }

                if (isset($video['family_friendly']) && is_bool($video['family_friendly'])) {
                    $videoData['family_friendly'] = $video['family_friendly'];
                }

                if (isset($video['requires_subscription']) && is_bool($video['requires_subscription'])) {
                    $videoData['requires_subscription'] = $video['requires_subscription'];
                }

                if (isset($video['live']) && is_bool($video['live'])) {
                    $videoData['live'] = $video['live'];
                }

                $processedSitemapVideos[] = $videoData;
            }
        }

        return [
            'url' => $url,
            'imageUrl' => $imageURL,
            'title' => $title,
            'description' => $description,
            'updatedAt' => $updatedAt,
            'tags' => $tags,
            'author' => $author,
            'publisher' => $publisher,
            'sitemapImages' => $processedSitemapImages,
            'sitemapVideos' => $processedSitemapVideos,
        ];
    }

    /** @return array<int, class-string<Model>> */
    public function seoModels(): array
    {
        $path = app_path('Models');

        $models = [];

        if (! is_dir($path)) {
            return $models;
        }

        foreach (File::allFiles($path) as $file) {
            $class = $this->classFromFile($file->getPathname());
            if (! $class) {
                continue;
            }
            if (! class_exists($class)) {
                continue;
            }

            $reflection = new ReflectionClass($class);
            if ($reflection->isAbstract()) {
                continue;
            }
            if (! is_subclass_of($class, Model::class)) {
                continue;
            }

            if (in_array(
                InteractsWithSEO::class,
                class_uses_recursive($class),
                true
            )) {
                $models[] = $class;
            }
        }

        return array_unique($models);
    }

    private function classFromFile(string $path): ?string
    {
        $contents = file_get_contents($path);

        if ($contents === false) {
            return null;
        }

        if (
            ! preg_match('/namespace\s+(.+?);/', $contents, $ns) ||
            ! preg_match('/class\s+(\w+)/', $contents, $cls)
        ) {
            return null;
        }

        return $ns[1].'\\'.$cls[1];
    }

    private function normalizeImageUrl(?string $imagePath): ?string
    {
        if (! filled($imagePath)) {
            return null;
        }

        /** @var string|null $result */
        $result = pipeline()
            ->send($imagePath)
            ->through([
                function (string $path, Closure $next): mixed {
                    if (preg_match('/^https?:\/\//i', $path)) {
                        return $path;
                    }

                    return $next($path);
                },
                function (string $path): string {
                    /** @var string $appUrlConfig */
                    $appUrlConfig = config('app.url') ?? '';
                    $appUrl = mb_rtrim($appUrlConfig, '/');
                    $cleanPath = mb_ltrim($path, '/');

                    if (str_starts_with($cleanPath, 'storage/')) {
                        return $appUrl.'/'.$cleanPath;
                    }

                    return $appUrl.'/storage/'.$cleanPath;
                },
            ])
            ->thenReturn();

        return $result;
    }
}
