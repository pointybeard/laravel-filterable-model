<?php

declare(strict_types=1);

namespace Pointybeard\FilterableModel\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Pointybeard\FilterableModel\Filter;

interface FilterableModelInterface
{
    public function scopeFilter(Builder $query, Filter $filter): Builder;

    /**
     * @return array<int, string>
     */
    public function getFilterable(): array;

    public function getSortable(): bool;

    public function getSortByDefault(): string;

    public function getSortOrderDefault(): string;
}
