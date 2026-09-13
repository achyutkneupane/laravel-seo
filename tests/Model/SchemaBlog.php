<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Tests\Model;

use AchyutN\LaravelSEO\Contracts\HasMarkup;
use AchyutN\LaravelSEO\Schemas\BlogSchema;
use AchyutN\LaravelSEO\Traits\InteractsWithSEO;
use Illuminate\Database\Eloquent\Model;

final class SchemaBlog extends Model implements HasMarkup
{
    use BlogSchema;
    use InteractsWithSEO;

    protected $table = 'blogs';

    protected $guarded = [];

    public function titleValue(): ?string
    {
        return 'Schema Blog';
    }

    public function urlValue(): ?string
    {
        return 'https://example.com/blog/schema';
    }

    public function authorValue(): ?string
    {
        return 'Jane Doe';
    }

    public function publisherValue(): ?string
    {
        return 'Acme Inc';
    }

    public function publisherUrlValue(): ?string
    {
        return 'https://example.com';
    }

    public function seoArticleBody(): ?string
    {
        return 'The complete article body.';
    }
}
