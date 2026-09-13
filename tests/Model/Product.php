<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Tests\Model;

use AchyutN\LaravelSEO\Contracts\HasMarkup;
use AchyutN\LaravelSEO\Schemas\ProductSchema;
use AchyutN\LaravelSEO\Traits\InteractsWithSEO;
use Illuminate\Database\Eloquent\Model;

final class Product extends Model implements HasMarkup
{
    use InteractsWithSEO;
    use ProductSchema;

    protected $table = 'blogs';

    protected $guarded = [];

    public function titleValue(): ?string
    {
        return 'Test Product';
    }

    public function urlValue(): ?string
    {
        return 'https://example.com/products/test';
    }

    public function priceValue(): ?float
    {
        return 100.0;
    }

    public function discountPriceValue(): ?float
    {
        return 80.0;
    }

    public function currencyValue(): ?string
    {
        return 'USD';
    }

    public function availabilityValue(): bool
    {
        return true;
    }

    public function skuValue(): ?string
    {
        return 'SKU-001';
    }

    public function brandValue(): ?string
    {
        return 'Acme';
    }
}
