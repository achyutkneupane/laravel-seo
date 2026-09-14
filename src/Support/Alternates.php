<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Support;

final class Alternates
{
    /**
     * Normalize a raw alternate list into hreflang/url pairs, skipping invalid entries.
     *
     * @return array<int, array{hreflang: string, url: string}>
     */
    public static function normalize(mixed $alternates): array
    {
        if (! is_iterable($alternates)) {
            return [];
        }

        $normalized = [];

        foreach ($alternates as $alternate) {
            if (! is_array($alternate)) {
                continue;
            }

            $hreflang = $alternate['hreflang'] ?? null;
            $url = $alternate['url'] ?? null;

            if (! is_string($hreflang) || ! is_string($url)) {
                continue;
            }

            $hreflang = mb_trim($hreflang);
            $url = mb_trim($url);

            if ($hreflang === '' || $url === '') {
                continue;
            }

            $normalized[] = ['hreflang' => $hreflang, 'url' => $url];
        }

        return $normalized;
    }
}
