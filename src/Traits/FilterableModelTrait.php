<?php

declare(strict_types=1);

namespace Pointybeard\FilterableModel\Traits;

use Illuminate\Database\Eloquent\Builder;
use Pointybeard\FilterableModel\Filter;

trait FilterableModelTrait
{
    public function scopeFilter(Builder $query, Filter $filter): Builder
    {
        return $filter->apply($this, $query);
    }

    public function getFilterable(): array
    {
        return $this->filterable;
    }

    public function getSortable(): bool
    {
        return $this->sortable;
    }

    public function getSortByDefault(): string
    {
        return $this->sortByDefault ?? 'created_at';
    }

    public function getSortOrderDefault(): string
    {
        return $this->sortOrderDefault ?? 'asc';
    }
}
