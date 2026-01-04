<?php

namespace AchyutN\LaravelSEO\Data;

use Illuminate\Support\Carbon;

/**
 * @phpstan-type AuthorArray array{"@type": string, name: string, url: string|null}
 */
final class ResolvedSEO
{
    /**
     * @param array<int, string> $tags
     */
    public function __construct(
        public string $title,
        public ?string $description,
        public ?string $url,
        public ?string $category,
        public array $tags,
        public string $author,
        public ?string $authorUrl,
        public string $publisher,
        public ?string $publisherUrl,
        public ?string $image,
        public ?Carbon $publishedAt,

        public ?string $pageType,

        public ?string $brand,
        public ?string $price,
        public ?string $currency,
        public bool $isAvailable = false,
        public ?string $sku,
    ) {}

    /** @return AuthorArray[] */
    public function authorArray(): array
    {
        $authorData = [
            '@type' => 'Person',
            'name' => $this->author,
            'url' => $this->authorUrl ?? null,
        ];

        return [$authorData];
    }

    /** @return AuthorArray */
    public function brandArray(): array
    {
        return [
            '@type' => 'Brand',
            'name' => $this->brand,
        ];
    }

    /** @return AuthorArray[] */
    public function publisherArray(): array
    {
        $publisherData = [
            '@type' => 'Organization',
            'name' => $this->publisher,
            'url' => $this->publisherUrl ?? null,
        ];

        return [$publisherData];
    }

    /** @return AuthorArray[] */
    public function authorAndPublisher(): array
    {
        return array_merge(
            $this->publisherArray(),
            $this->authorArray()
        );
    }
}
