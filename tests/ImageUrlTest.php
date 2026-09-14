<?php

declare(strict_types=1);

use AchyutN\LaravelSEO\Support\ImageUrl;

it('normalizes image paths to absolute urls', function (): void {
    expect(ImageUrl::normalize('images/a.jpg'))->toBe('http://localhost/storage/images/a.jpg')
        ->and(ImageUrl::normalize('storage/a.jpg'))->toBe('http://localhost/storage/a.jpg')
        ->and(ImageUrl::normalize('/storage/a.jpg'))->toBe('http://localhost/storage/a.jpg')
        ->and(ImageUrl::normalize('//cdn.example.com/a.jpg'))->toBe('https://cdn.example.com/a.jpg')
        ->and(ImageUrl::normalize('https://cdn.example.com/a.jpg'))->toBe('https://cdn.example.com/a.jpg')
        ->and(ImageUrl::normalize(null))->toBeNull();
});
