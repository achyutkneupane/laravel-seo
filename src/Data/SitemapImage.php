<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Data;

final readonly class SitemapImage
{
    public function __construct(
        public string $url,
        public ?string $title = null,
        public ?string $caption = null,
        public ?string $geoLocation = null,
        public ?string $license = null,
    ) {}

    public static function make(
        string $url,
        ?string $title = null,
        ?string $caption = null,
        ?string $geoLocation = null,
        ?string $license = null,
    ): self {
        return new self(
            url: $url,
            title: $title,
            caption: $caption,
            geoLocation: $geoLocation,
            license: $license,
        );
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function getGeoLocation(): ?string
    {
        return $this->geoLocation;
    }

    public function getLicense(): ?string
    {
        return $this->license;
    }

    /** @return array{loc: string, title: string|null, caption: string|null, geo_location: string|null, license: string|null} */
    public function toArray(): array
    {
        return [
            'loc' => $this->url,
            'title' => $this->title,
            'caption' => $this->caption,
            'geo_location' => $this->geoLocation,
            'license' => $this->license,
        ];
    }
}
