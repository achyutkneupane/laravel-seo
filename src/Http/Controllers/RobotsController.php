<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Http\Controllers;

use Illuminate\Http\Response;

final class RobotsController
{
    public function __invoke(): Response
    {
        $lines = ['User-agent: '.$this->userAgent()];

        /** @var array<int, mixed> $allow */
        $allow = (array) config('seo.robots_txt.allow', ['/']);
        foreach ($allow as $path) {
            if (is_string($path) && $path !== '') {
                $lines[] = 'Allow: '.$path;
            }
        }

        /** @var array<int, mixed> $disallow */
        $disallow = (array) config('seo.robots_txt.disallow', []);
        foreach ($disallow as $path) {
            if (is_string($path) && $path !== '') {
                $lines[] = 'Disallow: '.$path;
            }
        }

        $lines[] = '';
        $lines[] = 'Sitemap: '.$this->sitemapUrl();

        return response(implode("\n", $lines)."\n")
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    private function userAgent(): string
    {
        $userAgent = config('seo.robots_txt.user_agent');

        return is_string($userAgent) && $userAgent !== '' ? $userAgent : '*';
    }

    private function sitemapUrl(): string
    {
        $sitemap = config('seo.sitemap');
        $sitemap = is_string($sitemap) && $sitemap !== '' ? $sitemap : '/sitemap.xml';

        return str_starts_with($sitemap, 'http') ? $sitemap : url($sitemap);
    }
}
