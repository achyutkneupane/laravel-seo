---
name: laravel-seo
description: Apply opiniated conventions for the achyutn/laravel-seo package to add, manage, and configure SEO metadata, sitemaps, and schema.org markup for Laravel Eloquent models. Use this when defining SEO behavior, custom metadata mapping, or schema generation on models.
license: MIT
metadata:
  author: Achyut Neupane
---

# AchyutN Laravel SEO Guidelines

## Overview

Apply the `achyutn/laravel-seo` package guidelines to seamlessly integrate SEO metadata, Open Graph tags, Schema.org JSON-LD, and sitemaps into Laravel Eloquent models.

## When to Activate

- Activate this skill when the user asks to add SEO support to a Laravel Eloquent model.
- Activate this skill when managing meta titles, descriptions, images, or schema.org markup for database-driven pages (like articles, products, or generic pages).
- Activate this skill when working with sitemaps (`sitemap.xml`, `sitemap.txt`) or SEO data backfilling commands.
- Activate this skill when customizing the mapping of model attributes to SEO properties.

## Scope

- **In scope:** Eloquent models, SEO attribute mapping (traits/contracts), Schema.org traits, Breadcrumbs, Artisan commands (`seo:generate`), SEO model relationships.
- **Out of scope:** Frontend blade directive rendering (primarily handled by the underlying `ralphjsmit/laravel-seo` package), non-database static page SEO, JS/TS implementations.

## Workflow

1. Identify the Eloquent model that requires SEO capabilities.
2. Apply the `AchyutN\LaravelSEO\Traits\InteractsWithSEO` trait to the model.
3. Determine how the model's attributes map to SEO properties (e.g., title, description, image).
4. Map attributes by either defining public properties (e.g., `public string $titleColumn = 'name';`) or overriding value methods (e.g., `public function titleValue(): ?string`).
5. If the model requires Schema.org markup, implement the `AchyutN\LaravelSEO\Contracts\HasMarkup` contract and apply the appropriate schema trait (e.g., `BlogSchema`, `PageSchema`, `ProductSchema`).
6. Remind the user to run `php artisan seo:generate` if applying this to a model that already has existing database records.

## Core Rules (Summary)

- Models must use the `InteractsWithSEO` trait. This trait automatically hooks into the `created` event to generate an associated `SEO` model.
- Attribute resolution cascades: it first checks for a custom method (e.g., `titleValue()`), then falls back to a dynamically defined column property (e.g., `$titleColumn`), and finally defaults to a standard column name (e.g., `title`).
- Supported mapped columns include: title, description, category, image, author, author_url, publisher, publisher_url, tags, url, published_at, modified_at, page_type, brand, price, discount_price, currency, availability, and sku.
- The package automatically registers `/sitemap.xml` and `/sitemap.txt` routes; manual registration is not needed.

## Do and Don't

**Do:**
- Use the `InteractsWithSEO` trait to automatically handle SEO model creation and updates.
- Define custom attribute mappings using properties like `public string $titleColumn = 'heading';` or `public string $imageColumn = 'thumbnail';`.
- Use specific value methods like `public function getDescriptionValue(): ?string` or `public function tagsValue(): ?array` for complex logic or computed SEO properties.
- Return an array of `AchyutN\LaravelSEO\Data\Breadcrumb` objects in the `breadcrumbs()` method if the model should generate BreadcrumbList schema.
- Implement the `HasMarkup` contract and use traits like `BlogSchema` for articles or `ProductSchema` for e-commerce items.

**Don't:**
- Don't manually hook into Model events to create the `SEO` relationship; `InteractsWithSEO` boots this automatically via `bootInteractsWithSEO()`.
- Don't manually define sitemap generation logic; rely on the package's `SitemapService` and default routes.
- Don't manually construct massive SEO arrays; use the provided column mapping overrides.

## Examples

- See `references/code-examples.md` for detailed examples of basic model setup, complex computed values, breadcrumbs, and schema generation.
