<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Services;

use AchyutN\LaravelSEO\Models\SEO;
use Illuminate\Http\Response;
use Illuminate\Support\LazyCollection;

final class SitemapService
{
    public function toXML(): Response
    {
        $seoModels = $this->getSEOEntries();

        $xml = [];

        $xml[] = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';

        foreach ($seoModels as $seoModel) {
            $model = $seoModel->model;

            if (! $model || ! $model->getUrlValue()) continue;

            $xml[] = '<url>';
            $xml[] = '<loc>'.htmlspecialchars($model->getUrlValue(), ENT_XML1, 'UTF-8').'</loc>';
            if ($model->updated_at) {
                $xml[] = '<lastmod>'.htmlspecialchars($model->updated_at->toAtomString(), ENT_XML1, 'UTF-8').'</lastmod>';
            }

            if ($model->getImageValue()) {
                $xml[] = '<image:image>';
                $xml[] = '<image:loc>'.htmlspecialchars($model->getImageValue(), ENT_XML1, 'UTF-8').'</image:loc>';
                $xml[] = '<image:title>'.htmlspecialchars($model->getTitleValue() ?? '', ENT_XML1, 'UTF-8').'</image:title>';
                $xml[] = '<image:caption>'.htmlspecialchars($model->getDescriptionValue() ?? '', ENT_XML1, 'UTF-8').'</image:caption>';
                $xml[] = '</image:image>';
            }
            $xml[] = '</url>';
        }

        $xml[] = '</urlset>';

        return response(implode("\n", $xml))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function toTXT(): Response
    {
        $seoModels = $this->getSEOEntries();

        $txt = [];

        foreach ($seoModels as $seoModel) {
            $model = $seoModel->model;

            if (! $model || ! $model->getUrlValue()) continue;

            $txt[] = $model->getUrlValue();
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
            ->lazy()
            ->each(function (SEO $seoModel) {
                $seoModel->load('model');
            });
    }
}
