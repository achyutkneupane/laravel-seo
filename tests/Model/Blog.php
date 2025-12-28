<?php

declare(strict_types=1);

namespace LaravelSEO\Tests\Model;

use Illuminate\Database\Eloquent\Model;
use LaravelSEO\Traits\HasColumns;

final class Blog extends Model
{
    use HasColumns;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'published_at' => 'datetime',
        ];
    }
}
