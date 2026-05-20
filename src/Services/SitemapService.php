<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Services;

use AchyutN\LaravelSEO\Models\SEO;
use Illuminate\Http\Response;
use Illuminate\Support\LazyCollection;

final class SitemapService
{
    public function __construct(
        public SEOService $service
    ) {
        //
    }

    public function toXML(): Response
    {
        /** @var LazyCollection<int, SEO> $seoModels */
        $seoModels = $this->getSEOEntries();

        $xml = [];

        $xml[] = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">';

        foreach ($seoModels as $seoModel) {
            $model = $seoModel->model;

            if (! $model) {
                continue;
            }

            [
                'url' => $url,
                'imageUrl' => $imageUrl,
                'title' => $title,
                'description' => $description,
                'updatedAt' => $updatedAt,
                'sitemapImages' => $sitemapImages,
                'sitemapVideos' => $sitemapVideos,
            ] = $this->service->getModelValues($model);

            if ($url === null) {
                continue;
            }

            $xml[] = '<url>';
            $xml[] = '<loc>'.htmlspecialchars($url, ENT_XML1, 'UTF-8').'</loc>';
            if ($updatedAt) {
                $xml[] = '<lastmod>'.htmlspecialchars($updatedAt->toAtomString(), ENT_XML1, 'UTF-8').'</lastmod>';
            }

            if ($imageUrl && empty($sitemapImages)) {
                $xml[] = '<image:image>';
                $xml[] = '<image:loc>'.htmlspecialchars($imageUrl, ENT_XML1, 'UTF-8').'</image:loc>';
                $xml[] = '<image:title>'.htmlspecialchars($title ?? '', ENT_XML1, 'UTF-8').'</image:title>';
                $xml[] = '<image:caption>'.htmlspecialchars($description ?? '', ENT_XML1, 'UTF-8').'</image:caption>';
                $xml[] = '</image:image>';
            }

            foreach ($sitemapImages as $sitemapImageUrl) {
                $xml[] = '<image:image>';
                $xml[] = '<image:loc>'.htmlspecialchars($sitemapImageUrl, ENT_XML1, 'UTF-8').'</image:loc>';
                if (count($sitemapImages) === 1) {
                    $xml[] = '<image:title>'.htmlspecialchars($title ?? '', ENT_XML1, 'UTF-8').'</image:title>';
                    $xml[] = '<image:caption>'.htmlspecialchars($description ?? '', ENT_XML1, 'UTF-8').'</image:caption>';
                }
                $xml[] = '</image:image>';
            }

            foreach ($sitemapVideos as $video) {
                /** @var array{thumbnail_loc: string, title: string, description: string, content_loc?: string, player_loc?: string, duration?: int|float|string, publication_date?: string, expiration_date?: string, rating?: int|float|string, view_count?: int|float|string, family_friendly?: bool, requires_subscription?: bool, live?: bool} $video */
                $xml[] = '<video:video>';
                $xml[] = '<video:thumbnail_loc>'.htmlspecialchars($video['thumbnail_loc'], ENT_XML1, 'UTF-8').'</video:thumbnail_loc>';
                $xml[] = '<video:title>'.htmlspecialchars($video['title'], ENT_XML1, 'UTF-8').'</video:title>';
                $xml[] = '<video:description>'.htmlspecialchars($video['description'], ENT_XML1, 'UTF-8').'</video:description>';

                if (isset($video['content_loc'])) {
                    $xml[] = '<video:content_loc>'.htmlspecialchars($video['content_loc'], ENT_XML1, 'UTF-8').'</video:content_loc>';
                }
                if (isset($video['player_loc'])) {
                    $xml[] = '<video:player_loc>'.htmlspecialchars($video['player_loc'], ENT_XML1, 'UTF-8').'</video:player_loc>';
                }
                if (isset($video['duration'])) {
                    $xml[] = '<video:duration>'.(int) $video['duration'].'</video:duration>';
                }
                if (isset($video['publication_date'])) {
                    $xml[] = '<video:publication_date>'.htmlspecialchars($video['publication_date'], ENT_XML1, 'UTF-8').'</video:publication_date>';
                }
                if (isset($video['expiration_date'])) {
                    $xml[] = '<video:expiration_date>'.htmlspecialchars($video['expiration_date'], ENT_XML1, 'UTF-8').'</video:expiration_date>';
                }
                if (isset($video['rating'])) {
                    $xml[] = '<video:rating>'.(float) $video['rating'].'</video:rating>';
                }
                if (isset($video['view_count'])) {
                    $xml[] = '<video:view_count>'.(int) $video['view_count'].'</video:view_count>';
                }
                if (isset($video['family_friendly'])) {
                    $xml[] = '<video:family_friendly>'.($video['family_friendly'] ? 'yes' : 'no').'</video:family_friendly>';
                }
                if (isset($video['requires_subscription'])) {
                    $xml[] = '<video:requires_subscription>'.($video['requires_subscription'] ? 'yes' : 'no').'</video:requires_subscription>';
                }
                if (isset($video['live'])) {
                    $xml[] = '<video:live>'.($video['live'] ? 'yes' : 'no').'</video:live>';
                }
                $xml[] = '</video:video>';
            }
            $xml[] = '</url>';
        }

        $xml[] = '</urlset>';

        return response(implode("\n", $xml))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function toTXT(): Response
    {
        /** @var LazyCollection<int, SEO> $seoModels */
        $seoModels = $this->getSEOEntries();

        $txt = [];

        foreach ($seoModels as $seoModel) {
            $model = $seoModel->model;

            if (! $model) {
                continue;
            }

            /** @phpstan-var string|null $url */
            [
                'url' => $url
            ] = $this->service->getModelValues($model);

            if ($url !== null) {
                $txt[] = $url;
            }
        }

        return response(implode("\n", $txt))
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    /** @return LazyCollection<int, SEO> */
    private function getSEOEntries(): LazyCollection
    {
        return SEO::query()
            ->with('model')
            ->orderBy('model_type')
            ->lazy();
    }
}
