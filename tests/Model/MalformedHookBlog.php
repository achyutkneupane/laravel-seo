<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Tests\Model;

use AchyutN\LaravelSEO\Traits\InteractsWithSEO;
use Illuminate\Database\Eloquent\Model;

final class MalformedHookBlog extends Model
{
    use InteractsWithSEO;

    protected $table = 'blogs';

    protected $guarded = [];

    /** @return array<int, array<string, mixed>> */
    public function seoFaqs(): array
    {
        return [
            ['not_a_question' => 'nope'],
        ];
    }

    /** @return array<string, mixed> */
    public function seoHowTo(): array
    {
        return [
            'steps' => [],
        ];
    }

    /** @return array<int, mixed> */
    public function seoSpeakable(): array
    {
        return [123, null];
    }
}
