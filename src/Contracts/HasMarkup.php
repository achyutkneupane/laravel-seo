<?php

namespace AchyutN\LaravelSEO\Contracts;

use RalphJSmit\Laravel\SEO\SchemaCollection;

interface HasMarkup
{
    public function buildSchema(SchemaCollection $schema): SchemaCollection;
}
