<?php

declare(strict_types=1);

namespace LaravelSEO\Tests;

use LaravelSEO\Tests\Model\Blog;

beforeEach(function () {
    $this->data = [
        'title' => 'My First Blog',
        'description' => 'This is the description of my first blog.',
        'tags' => ['Blog', 'Article', 'SEO'],
        'published_at' => now(),
    ];
});

it('has correct SEO title', function () {
    $blog = Blog::query()->create($this->data);

    $this->assertEquals(
        $this->data['title'],
        $blog->getTitleValue(),
    );
});
