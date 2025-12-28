<?php

declare(strict_types=1);

namespace LaravelSEO\Traits;

/**
 * @property string $titleColumn
 *
 * @method string|null titleValue
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

    protected function titleColumn(): string
    {
        return property_exists($this, 'titleColumn')
            ? $this->titleColumn
            : 'title';
    }
}
