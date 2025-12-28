<?php

declare(strict_types=1);

namespace LaravelSEO\Tests\Model;

use Illuminate\Database\Eloquent\Model;
use LaravelSEO\Traits\InteractsWithSEO;

final class Blog extends Model
{
    use InteractsWithSEO;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'published_at' => 'datetime',
        ];
    }
}
