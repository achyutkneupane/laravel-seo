<?php

declare(strict_types=1);

namespace LaravelSEO\Traits;

/**
 * @property string $titleColumn
 * @property string $descriptionColumn
 * @property string $imageColumn
 * @property string $authorColumn
 * @property string $publisherColumn
 * @property string $tagsColumn
 *
 * @method string|null titleValue
 * @method string|null descriptionValue
 * @method string|null imageValue
 * @method string|null authorValue
 * @method string|null publisherValue
 * @method array|null tagsValue
 */
trait HasColumns
{
    public function getTitleValue(): ?string
    {
        if (method_exists($this, 'titleValue') && $this->titleValue() !== null) {
            return $this->titleValue();
        }

        return data_get($this, $this->titleColumn());
    }

    public function getDescriptionValue(): ?string
    {
        if (method_exists($this, 'descriptionValue') && $this->descriptionValue() !== null) {
            return $this->descriptionValue();
        }

        return data_get($this, $this->descriptionColumn());
    }

    public function getImageValue(): ?string
    {
        if (method_exists($this, 'imageValue') && $this->imageValue() !== null) {
            return $this->imageValue();
        }

        return data_get($this, $this->imageColumn());
    }

    public function getAuthorValue(): ?string
    {
        if (method_exists($this, 'authorValue') && $this->authorValue() !== null) {
            return $this->authorValue();
        }

        return data_get($this, $this->authorColumn());
    }

    public function getPublisherValue(): ?string
    {
        if (method_exists($this, 'publisherValue') && $this->publisherValue() !== null) {
            return $this->publisherValue();
        }

        return data_get($this, $this->publisherColumn());
    }

    public function getTagsValue(): ?array
    {
        if (method_exists($this, 'tagsValue') && $this->tagsValue() !== null) {
            return $this->tagsValue();
        }

        return data_get($this, $this->tagsColumn());
    }

    protected function titleColumn(): string
    {
        return property_exists($this, 'titleColumn')
            ? $this->titleColumn
            : 'title';
    }

    protected function descriptionColumn(): string
    {
        return property_exists($this, 'descriptionColumn')
            ? $this->descriptionColumn
            : 'description';
    }

    protected function imageColumn(): string
    {
        return property_exists($this, 'imageColumn')
            ? $this->imageColumn
            : 'image';
    }

    protected function authorColumn(): string
    {
        return property_exists($this, 'authorColumn')
            ? $this->authorColumn
            : 'author';
    }

    protected function publisherColumn(): string
    {
        return property_exists($this, 'publisherColumn')
            ? $this->publisherColumn
            : 'publisher';
    }

    protected function tagsColumn(): string
    {
        return property_exists($this, 'tagsColumn')
            ? $this->tagsColumn
            : 'tags';
    }
}
