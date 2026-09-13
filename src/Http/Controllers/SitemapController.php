<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Http\Controllers;

use AchyutN\LaravelSEO\Services\SitemapService;
use Illuminate\Http\Response;

final class SitemapController
{
    public function xml(SitemapService $service): Response
    {
        return $service->toXML();
    }

    public function txt(SitemapService $service): Response
    {
        return $service->toTXT();
    }
}
