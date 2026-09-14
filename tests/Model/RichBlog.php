<?php

declare(strict_types=1);

namespace AchyutN\LaravelSEO\Tests\Model;

use AchyutN\LaravelSEO\Traits\InteractsWithSEO;
use Illuminate\Database\Eloquent\Model;

final class RichBlog extends Model
{
    use InteractsWithSEO;

    protected $table = 'blogs';

    protected $guarded = [];

    public function seoType(): string
    {
        return 'article';
    }

    public function seoLocale(): string
    {
        return 'en';
    }

    public function seoArticleBody(): string
    {
        return 'The complete article body.';
    }

    /**
     * @return array<int, array{hreflang: string, url: string}>
     */
    public function seoAlternates(): array
    {
        return [
            ['hreflang' => 'en', 'url' => 'https://example.com/en'],
            ['hreflang' => 'fr', 'url' => 'https://example.com/fr'],
        ];
    }

    /**
     * @return array<int, array{question: string, answer: string}>
     */
    public function seoFaqs(): array
    {
        return [
            ['question' => 'What is SEO?', 'answer' => 'Search engine optimization.'],
        ];
    }

    /**
     * @return array{name: string, description: string, steps: array<int, array{name: string, text: string}>}
     */
    public function seoHowTo(): array
    {
        return [
            'name' => 'Install the package',
            'description' => 'How to install the package.',
            'steps' => [
                ['name' => 'Require', 'text' => 'Run composer require.'],
                ['name' => 'Publish', 'text' => 'Publish the config.'],
            ],
        ];
    }

    /**
     * @return array<int, string>
     */
    public function seoSpeakable(): array
    {
        return ['#intro', '.summary'];
    }

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'published_at' => 'datetime',
        ];
    }
}
