<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Data;

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

    /** @return array<string, string> */
    public function toArray(): array
    {
        return [
            $this->label => $this->url,
        ];
    }
}

