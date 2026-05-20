<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Contracts;

use Illuminate\Support\Carbon;

interface HasColumns
{
    public function getTitleValue(): ?string;

    public function getDescriptionValue(): ?string;

    public function getCategoryValue(): ?string;

    public function getImageValue(): ?string;

    public function getAuthorValue(): ?string;

    public function getAuthorUrlValue(): ?string;

    public function getPublisherValue(): ?string;

    public function getPublisherUrlValue(): ?string;

    /** @return array<int, string>|null */
    public function getTagsValue(): ?array;

    public function getURLValue(): ?string;

    public function getPublishedAtValue(): ?Carbon;

    public function getModifiedAtValue(): ?Carbon;

    public function getPageTypeValue(): ?string;

    public function getBrandValue(): ?string;

    public function getPriceValue(): ?float;

    public function getdiscountPriceValue(): ?float;

    public function getCurrencyValue(): ?string;

    public function getAvailabilityValue(): bool;

    public function getSkuValue(): ?string;

    /** @return array<int, string> */
    public function getSitemapImagesValue(): array;

    /** @return array<int, array{thumbnail_loc: string, title: string, description: string, player_loc: string, duration?: int, publication_date?: string, expiration_date?: string, rating?: float, view_count?: int, family_friendly?: bool, requires_subscription?: bool, live?: bool}> */
    public function getSitemapVideosValue(): array;
}
