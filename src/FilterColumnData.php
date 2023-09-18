<?php

declare(strict_types=1);

namespace Pointybeard\FilterableModel;

use Spatie\LaravelData\Data;

class FilterColumnData extends Data
{
    public function __construct(
        public string $name,
        public mixed $value
    ) {
    }
}
