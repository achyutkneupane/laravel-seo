<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Services;

use AchyutN\LaravelSEO\Traits\InteractsWithSEO;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use ReflectionClass;

final class SEOService
{
    /** @return array{url: string|null, imageUrl: string|null, title: string|null, description: string|null, updatedAt: Carbon|null, tags: array<int, string>, author: string|null, publisher: string|null, sitemapImages: array<int, string>, sitemapVideos: array<int, array<string, mixed>>} */
    public function getModelValues(Model $model): array
    {
        /** @var string|null $url */
        $url = method_exists($model, 'getUrlValue') ? $model->getUrlValue() : null;
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
        /** @var array<int, string> $sitemapImages */
        $sitemapImages = method_exists($model, 'getSitemapImagesValue') ? $model->getSitemapImagesValue() : [];
        /** @var array<int, array<string, mixed>> $sitemapVideos */
        $sitemapVideos = method_exists($model, 'getSitemapVideosValue') ? $model->getSitemapVideosValue() : [];

        /** @var string $imageURL */
        $imageURL = pipeline()
            ->send($imagePath)
            ->through([
                function (?string $imagePath, Closure $next): mixed {
                    if (! filled($imagePath)) {
                        return null;
                    }

                    if (preg_match('/^https?:\/\//', $imagePath)) {
                        return $imagePath;
                    }

                    return $next($imagePath);
                },
                function (string $imagePath): string {
                    /** @phpstan-var string $url */
                    $url = config('app.url');

                    return $url.'/storage/'.$imagePath;
                },
            ])
            ->thenReturn();

        /** @var array<int, string> $processedSitemapImages */
        $processedSitemapImages = [];
        foreach ($sitemapImages as $sitemapImage) {
            /** @var string|null $processedImageUrl */
            $processedImageUrl = pipeline()
                ->send($sitemapImage)
                ->through([
                    function (?string $imagePath, Closure $next): mixed {
                        if (! filled($imagePath)) {
                            return null;
                        }

                        if (preg_match('/^https?:\/\//', $imagePath)) {
                            return $imagePath;
                        }

                        return $next($imagePath);
                    },
                    function (string $imagePath): string {
                        /** @phpstan-var string $url */
                        $url = config('app.url');

                        return $url.'/storage/'.$imagePath;
                    },
                ])
                ->thenReturn();

            if ($processedImageUrl !== null) {
                $processedSitemapImages[] = $processedImageUrl;
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
            'sitemapVideos' => $sitemapVideos,
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
}
