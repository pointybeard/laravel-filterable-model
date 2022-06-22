<?php

declare(strict_types=1);

namespace Pointybeard\FilterableModel;

use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

class FilterColumnCollection extends Collection
{
    /**
     * Get an item at a given offset.
     *
     * @param mixed $key
     *
     * @return FilterColumnDataTransferObject
     */
    public function offsetGet(mixed $key): FilterColumnDataTransferObject
    {
        // Illuminate\Support\Collection::offsetGet() defines type for $key as
        // mixed, however, it is used as offset into an array. Since array
        // offsets must always be either a string or an integer static
        // code analysers will complain without this assertion.
        Assert::true(is_string($key) || is_int($key), 'parameter #1 of method FilterColumnCollection::offsetGet() must be of type string or int');

        $result = parent::offsetGet($key);

        // This is for the benefit of static code analysis. parent::offsetGet
        // has return type of mixed. However, this method always returns
        // an instance of FilterColumnDataTransferObject. Without this
        // assert line, static code analysers will complain.
        Assert::isInstanceOf($result, FilterColumnDataTransferObject::class);

        return $result;
    }
}
