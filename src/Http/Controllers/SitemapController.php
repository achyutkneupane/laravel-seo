<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Http\Controllers;

use AchyutN\LaravelSEO\Services\SitemapService;
use Illuminate\Http\Response;

final class SitemapController
{
    public function xml(): Response
    {
        return app(SitemapService::class)->toXML();
    }

    public function txt(): Response
    {
        return app(SitemapService::class)->toTXT();
    }
}
