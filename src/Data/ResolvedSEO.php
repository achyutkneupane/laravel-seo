<?php

namespace AchyutN\LaravelSEO\Data;

use Illuminate\Support\Carbon;

final class ResolvedSEO
{
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
    ) {}

    public function authorArray(): array
    {
        $authorData = [
            '@type' => 'Person',
            'name' => $this->author,
        ];

        if ($this->authorUrl) {
            $authorData['url'] = $this->authorUrl;
        }

        return [$authorData];
    }

    public function publisherArray(): array
    {
        $publisherData = [
            '@type' => 'Organization',
            'name' => $this->publisher,
        ];

        if ($this->publisherUrl) {
            $publisherData['url'] = $this->publisherUrl;
        }

        return [$publisherData];
    }

    public function authorAndPublisher(): array
    {
        return array_merge(
            $this->publisherArray(),
            $this->authorArray()
        );
    }
}
