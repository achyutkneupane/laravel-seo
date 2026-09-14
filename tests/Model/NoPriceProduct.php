<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Tests\Model;

use AchyutN\LaravelSEO\Contracts\HasMarkup;
use AchyutN\LaravelSEO\Schemas\ProductSchema;
use AchyutN\LaravelSEO\Traits\InteractsWithSEO;
use Illuminate\Database\Eloquent\Model;

final class NoPriceProduct extends Model implements HasMarkup
{
    use InteractsWithSEO;
    use ProductSchema;

    protected $table = 'blogs';

    protected $guarded = [];

    public function titleValue(): ?string
    {
        return 'Free Product';
    }

    public function urlValue(): ?string
    {
        return 'https://example.com/products/free';
    }
}
