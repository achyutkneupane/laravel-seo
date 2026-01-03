<?php

declare(strict_types=1);

namespace LaravelSEO\Data;

final class Breadcrumb
{
    public function __construct(
        private string $label,
        private string $url,
    ) {
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}

