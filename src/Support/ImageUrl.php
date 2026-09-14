<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Support;

use Closure;

final class ImageUrl
{
    /**
     * Normalize an image path or URL into an absolute URL.
     */
    public static function normalize(?string $imagePath): ?string
    {
        if (! filled($imagePath)) {
            return null;
        }

        /** @var string|null $result */
        $result = pipeline()
            ->send($imagePath)
            ->through([
                function (string $path, Closure $next): mixed {
                    $path = mb_trim($path);

                    if (preg_match('/^https?:\/\//i', $path)) {
                        return $path;
                    }

                    if (str_starts_with($path, '//')) {
                        return 'https:'.$path;
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
