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
            $xml[] = '<loc>'.$this->escapeXml($url).'</loc>';
            if ($updatedAt instanceof \Illuminate\Support\Carbon) {
                $xml[] = '<lastmod>'.$this->escapeXml($updatedAt->toAtomString()).'</lastmod>';
            }

            if ($imageUrl && $sitemapImages === []) {
                $xml[] = '<image:image>';
                $xml[] = '<image:loc>'.$this->escapeXml($imageUrl).'</image:loc>';
                $xml[] = '<image:title>'.$this->escapeXml($title ?? '').'</image:title>';
                $xml[] = '<image:caption>'.$this->escapeXml($description ?? '').'</image:caption>';
                $xml[] = '</image:image>';
            }

            foreach ($sitemapImages as $image) {
                /** @var array{loc: string, title?: string|null, caption?: string|null, geo_location?: string|null, license?: string|null} $image */
                $imgLoc = $image['loc'];
                $imgTitle = $image['title'] ?? null;
                $imgCaption = $image['caption'] ?? null;
                $imgGeo = $image['geo_location'] ?? null;
                $imgLicense = $image['license'] ?? null;

                if (count($sitemapImages) === 1) {
                    $imgTitle ??= $title;
                    $imgCaption ??= $description;
                }

                $xml[] = '<image:image>';
                $xml[] = '<image:loc>'.$this->escapeXml($imgLoc).'</image:loc>';
                if ($imgTitle !== null && $imgTitle !== '') {
                    $xml[] = '<image:title>'.$this->escapeXml($imgTitle).'</image:title>';
                }
                if ($imgCaption !== null && $imgCaption !== '') {
                    $xml[] = '<image:caption>'.$this->escapeXml($imgCaption).'</image:caption>';
                }
                if ($imgGeo !== null && $imgGeo !== '') {
                    $xml[] = '<image:geo_location>'.$this->escapeXml($imgGeo).'</image:geo_location>';
                }
                if ($imgLicense !== null && $imgLicense !== '') {
                    $xml[] = '<image:license>'.$this->escapeXml($imgLicense).'</image:license>';
                }
                $xml[] = '</image:image>';
            }

            foreach ($sitemapVideos as $video) {
                /** @var array{thumbnail_loc: string, title: string, description: string, content_loc?: string, player_loc?: string, duration?: int|float|string, publication_date?: string, expiration_date?: string, rating?: int|float|string, view_count?: int|float|string, family_friendly?: bool, requires_subscription?: bool, live?: bool} $video */
                $xml[] = '<video:video>';
                $xml[] = '<video:thumbnail_loc>'.$this->escapeXml((string) $video['thumbnail_loc']).'</video:thumbnail_loc>';
                $xml[] = '<video:title>'.$this->escapeXml(mb_substr($video['title'], 0, 100)).'</video:title>';
                $xml[] = '<video:description>'.$this->escapeXml(mb_substr($video['description'], 0, 2048)).'</video:description>';

                if (isset($video['content_loc'])) {
                    $xml[] = '<video:content_loc>'.$this->escapeXml($video['content_loc']).'</video:content_loc>';
                }
                if (isset($video['player_loc'])) {
                    $xml[] = '<video:player_loc>'.$this->escapeXml($video['player_loc']).'</video:player_loc>';
                }
                if (isset($video['duration'])) {
                    $xml[] = '<video:duration>'.(int) $video['duration'].'</video:duration>';
                }
                if (isset($video['publication_date'])) {
                    $xml[] = '<video:publication_date>'.$this->escapeXml($video['publication_date']).'</video:publication_date>';
                }
                if (isset($video['expiration_date'])) {
                    $xml[] = '<video:expiration_date>'.$this->escapeXml($video['expiration_date']).'</video:expiration_date>';
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
                'url' => $url,
            ] = $this->service->getModelValues($model);

            if ($url !== null) {
                $sanitizedUrl = preg_replace('/[\r\n\t]+/', '', mb_trim($url));
                if ($sanitizedUrl !== null && $sanitizedUrl !== '') {
                    $txt[] = $sanitizedUrl;
                }
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

    private function escapeXml(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        /** @var string $clean */
        $clean = preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', '', $value) ?? '';

        return htmlspecialchars($clean, ENT_XML1, 'UTF-8');
    }
}
