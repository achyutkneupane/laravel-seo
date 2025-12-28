<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use LaravelSEO\Traits\InteractsWithSEO;

final class Blog extends Model
{
    use InteractsWithSEO;
}
