<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Traits;

use Illuminate\Support\Carbon;

/**
 * @property string $titleColumn
 * @property string $descriptionColumn
 * @property string $categoryColumn
 * @property string $imageColumn
 * @property string $authorColumn
 * @property string $authorUrlColumn
 * @property string $publisherColumn
 * @property string $publisherUrlColumn
 * @property string $tagsColumn
 * @property string $urlColumn
 * @property string $publishedAtColumn
 * @property string $modifiedAtColumn
 * @property string $pageTypeColumn
 * @property string $brandColumn
 * @property string $priceColumn
 * @property string $discountPriceColumn
 * @property string $currencyColumn
 * @property string $availabilityColumn
 * @property string $skuColumn
 *
 * @method string|null titleValue()
 * @method string|null descriptionValue()
 * @method string|null categoryValue()
 * @method string|null imageValue()
 * @method string|null authorValue()
 * @method string|null authorUrlValue()
 * @method string|null publisherValue()
 * @method string|null publisherUrlValue()
 * @method array<int, string>|null tagsValue()
 * @method string|null urlValue()
 * @method Carbon|null publishedAtValue()
 * @method Carbon|null modifiedAtValue()
 * @method string|null pageTypeValue()
 * @method string|null brandValue()
 * @method float|null priceValue()
 * @method float|null discountPriceValue()
 * @method string|null currencyValue()
 * @method bool availabilityValue()
 * @method string|null skuValue()
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

    public function getCategoryValue(): ?string
    {
        if (method_exists($this, 'categoryValue') && $this->categoryValue() !== null) {
            return $this->categoryValue();
        }

        return data_get($this, $this->categoryColumn());
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

    public function getAuthorUrlValue(): ?string
    {
        if (method_exists($this, 'authorUrlValue') && $this->authorUrlValue() !== null) {
            return $this->authorUrlValue();
        }

        return data_get($this, $this->authorUrlColumn());
    }

    public function getPublisherValue(): ?string
    {
        if (method_exists($this, 'publisherValue') && $this->publisherValue() !== null) {
            return $this->publisherValue();
        }

        return data_get($this, $this->publisherColumn());
    }

    public function getPublisherUrlValue(): ?string
    {
        if (method_exists($this, 'publisherUrlValue') && $this->publisherUrlValue() !== null) {
            return $this->publisherUrlValue();
        }

        return data_get($this, $this->publisherUrlColumn());
    }

    public function getTagsValue(): ?array
    {
        if (method_exists($this, 'tagsValue') && $this->tagsValue() !== null) {
            return $this->tagsValue();
        }

        return data_get($this, $this->tagsColumn());
    }

    public function getURLValue(): ?string
    {
        if (method_exists($this, 'urlValue') && $this->urlValue() !== null) {
            return $this->urlValue();
        }

        return data_get($this, $this->urlColumn());
    }

    public function getPublishedAtValue(): ?Carbon
    {
        if (method_exists($this, 'publishedAtValue') && $this->publishedAtValue() !== null) {
            return $this->publishedAtValue();
        }

        return data_get($this, $this->publishedAtColumn());
    }

    public function getModifiedAtValue(): ?Carbon
    {
        if (method_exists($this, 'modifiedAtValue') && $this->modifiedAtValue() !== null) {
            return $this->modifiedAtValue();
        }

        return data_get($this, $this->modifiedAtColumn());
    }

    public function getPageTypeValue(): ?string
    {
        if (method_exists($this, 'pageTypeValue') && $this->pageTypeValue() !== null) {
            return $this->pageTypeValue();
        }

        return data_get($this, $this->pageTypeColumn());
    }

    public function getBrandValue(): ?string
    {
        if (method_exists($this, 'brandValue') && $this->brandValue() !== null) {
            return $this->brandValue();
        }

        return data_get($this, $this->brandColumn());
    }

    public function getPriceValue(): ?float
    {
        if (method_exists($this, 'priceValue') && $this->priceValue() !== null) {
            return $this->priceValue();
        }

        return data_get($this, $this->priceColumn());
    }

    public function getdiscountPriceValue(): ?float
    {
        if (method_exists($this, 'discountPriceValue') && $this->discountPriceValue() !== null) {
            return $this->discountPriceValue();
        }

        return data_get($this, $this->discountPriceColumn());
    }

    public function getCurrencyValue(): ?string
    {
        if (method_exists($this, 'currencyValue') && $this->currencyValue() !== null) {
            return $this->currencyValue();
        }

        return data_get($this, $this->currencyColumn());
    }

    public function getAvailabilityValue(): bool
    {
        if (method_exists($this, 'availabilityValue') && $this->availabilityValue() !== null) {
            return $this->availabilityValue();
        }

        return data_get($this, $this->availabilityColumn()) ?? false;
    }

    public function getSkuValue(): ?string
    {
        if (method_exists($this, 'skuValue') && $this->skuValue() !== null) {
            return $this->skuValue();
        }

        return data_get($this, $this->skuColumn());
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

    protected function categoryColumn(): string
    {
        return property_exists($this, 'categoryColumn')
            ? $this->categoryColumn
            : 'category';
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

    protected function authorUrlColumn(): string
    {
        return property_exists($this, 'authorUrlColumn')
            ? $this->authorUrlColumn
            : 'author_url';
    }

    protected function publisherColumn(): string
    {
        return property_exists($this, 'publisherColumn')
            ? $this->publisherColumn
            : 'publisher';
    }

    protected function publisherUrlColumn(): string
    {
        return property_exists($this, 'publisherUrlColumn')
            ? $this->publisherUrlColumn
            : 'publisher_url';
    }

    protected function tagsColumn(): string
    {
        return property_exists($this, 'tagsColumn')
            ? $this->tagsColumn
            : 'tags';
    }

    protected function urlColumn(): string
    {
        return property_exists($this, 'urlColumn')
            ? $this->urlColumn
            : 'url';
    }

    protected function publishedAtColumn(): string
    {
        return property_exists($this, 'publishedAtColumn')
            ? $this->publishedAtColumn
            : 'created_at';
    }

    protected function modifiedAtColumn(): string
    {
        return property_exists($this, 'modifiedAtColumn')
            ? $this->modifiedAtColumn
            : 'updated_at';
    }

    protected function pageTypeColumn(): string
    {
        return property_exists($this, 'pageTypeColumn')
            ? $this->pageTypeColumn
            : 'page_type';
    }

    protected function brandColumn(): string
    {
        return property_exists($this, 'brandColumn')
            ? $this->brandColumn
            : 'brand';
    }

    protected function priceColumn(): string
    {
        return property_exists($this, 'priceColumn')
            ? $this->priceColumn
            : 'price';
    }

    protected function discountPriceColumn(): string
    {
        return property_exists($this, 'discountPriceColumn')
            ? $this->discountPriceColumn
            : 'discount_price';
    }

    protected function currencyColumn(): string
    {
        return property_exists($this, 'currencyColumn')
            ? $this->currencyColumn
            : 'currency';
    }

    protected function availabilityColumn(): string
    {
        return property_exists($this, 'availabilityColumn')
            ? $this->availabilityColumn
            : 'is_available';
    }

    protected function skuColumn(): string
    {
        return property_exists($this, 'skuColumn')
            ? $this->skuColumn
            : 'sku';
    }
}
