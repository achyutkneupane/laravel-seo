<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Data;

final readonly class SitemapVideo
{
    public function __construct(
        public string $thumbnailLoc,
        public string $title,
        public string $description,
        public ?string $playerLoc = null,
        public ?string $contentLoc = null,
        public ?int $duration = null,
        public ?string $publicationDate = null,
        public ?string $expirationDate = null,
        public ?float $rating = null,
        public ?int $viewCount = null,
        public ?bool $familyFriendly = null,
        public ?bool $requiresSubscription = null,
        public ?bool $live = null,
    ) {}

    public static function make(
        string $thumbnailLoc,
        string $title,
        string $description,
        ?string $playerLoc = null,
        ?string $contentLoc = null,
        ?int $duration = null,
        ?string $publicationDate = null,
        ?string $expirationDate = null,
        ?float $rating = null,
        ?int $viewCount = null,
        ?bool $familyFriendly = null,
        ?bool $requiresSubscription = null,
        ?bool $live = null,
    ): self {
        return new self(
            thumbnailLoc: $thumbnailLoc,
            title: $title,
            description: $description,
            playerLoc: $playerLoc,
            contentLoc: $contentLoc,
            duration: $duration,
            publicationDate: $publicationDate,
            expirationDate: $expirationDate,
            rating: $rating,
            viewCount: $viewCount,
            familyFriendly: $familyFriendly,
            requiresSubscription: $requiresSubscription,
            live: $live,
        );
    }

    public function getThumbnailLoc(): string
    {
        return $this->thumbnailLoc;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPlayerLoc(): ?string
    {
        return $this->playerLoc;
    }

    public function getContentLoc(): ?string
    {
        return $this->contentLoc;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function getPublicationDate(): ?string
    {
        return $this->publicationDate;
    }

    public function getExpirationDate(): ?string
    {
        return $this->expirationDate;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function getViewCount(): ?int
    {
        return $this->viewCount;
    }

    public function isFamilyFriendly(): ?bool
    {
        return $this->familyFriendly;
    }

    public function requiresSubscription(): ?bool
    {
        return $this->requiresSubscription;
    }

    public function isLive(): ?bool
    {
        return $this->live;
    }

    /**
     * @return array{
     *     thumbnail_loc: string,
     *     title: string,
     *     description: string,
     *     player_loc?: string,
     *     content_loc?: string,
     *     duration?: int,
     *     publication_date?: string,
     *     expiration_date?: string,
     *     rating?: float,
     *     view_count?: int,
     *     family_friendly?: bool,
     *     requires_subscription?: bool,
     *     live?: bool
     * }
     */
    public function toArray(): array
    {
        $data = [
            'thumbnail_loc' => $this->thumbnailLoc,
            'title' => $this->title,
            'description' => $this->description,
        ];

        if ($this->playerLoc !== null) {
            $data['player_loc'] = $this->playerLoc;
        }

        if ($this->contentLoc !== null) {
            $data['content_loc'] = $this->contentLoc;
        }

        if ($this->duration !== null) {
            $data['duration'] = $this->duration;
        }

        if ($this->publicationDate !== null) {
            $data['publication_date'] = $this->publicationDate;
        }

        if ($this->expirationDate !== null) {
            $data['expiration_date'] = $this->expirationDate;
        }

        if ($this->rating !== null) {
            $data['rating'] = $this->rating;
        }

        if ($this->viewCount !== null) {
            $data['view_count'] = $this->viewCount;
        }

        if ($this->familyFriendly !== null) {
            $data['family_friendly'] = $this->familyFriendly;
        }

        if ($this->requiresSubscription !== null) {
            $data['requires_subscription'] = $this->requiresSubscription;
        }

        if ($this->live !== null) {
            $data['live'] = $this->live;
        }

        return $data;
    }
}
