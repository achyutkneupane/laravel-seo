<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Data;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @phpstan-type AuthorArray array{"@type": string, name: string, url: string|null}
 */
final class ResolvedSEO
{
    /**
     * @param  array<int, string>  $tags
     */
    public function __construct(
        private readonly Model $model,

        public string $title,
        public ?string $description,
        public ?string $url,
        public ?string $category,
        public array $tags,
        public ?string $author = null,
        public ?string $authorUrl = null,
        public ?string $publisher = null,
        public ?string $publisherUrl = null,
        public ?string $image = null,
        public ?Carbon $publishedAt = null,
        public ?Carbon $modifiedAt = null,

        public ?string $pageType = null,

        public ?string $brand = null,
        public ?float $price = null,
        public ?float $discountPrice = null,
        public ?string $currency = null,
        public bool $isAvailable = false,
        public ?string $sku = null,
        public ?string $articleBody = null,
    ) {
        //
    }

    /** @return AuthorArray[] */
    public function authorArray(): array
    {
        if ($this->author === null || $this->author === '') {
            return [];
        }

        $authorData = [
            '@type' => 'Person',
            'name' => $this->author,
            'url' => $this->authorUrl,
        ];

        return [$authorData];
    }

    /** @return AuthorArray */
    public function brandArray(): array
    {
        return [
            '@type' => 'Brand',
            'name' => $this->brand ?? $this->publisher ?? '',
            'url' => null,
        ];
    }

    /** @return AuthorArray[] */
    public function publisherArray(): array
    {
        if ($this->publisher === null || $this->publisher === '') {
            return [];
        }

        $publisherData = [
            '@type' => 'Organization',
            'name' => $this->publisher,
            'url' => $this->publisherUrl,
        ];

        return [$publisherData];
    }

    public function hasDiscount(): bool
    {
        return $this->discountPrice !== null && $this->price !== null && $this->discountPrice < $this->price;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * @deprecated Use authorArray() and publisherArray() instead.
     *
     * @return AuthorArray[]
     */
    public function authorAndPublisher(): array
    {
        return array_merge(
            $this->publisherArray(),
            $this->authorArray()
        );
    }
}
