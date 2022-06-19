<?php

declare(strict_types=1);

namespace Pointybeard\FilterableModel;

use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

class FilterColumnCollection extends Collection
{
    public function offsetGet($key): FilterColumnDataTransferObject
    {
        $result = parent::offsetGet($key);

        // This is for the benefit of static code analysis. parent::offsetGet
        // has return type of mixed. However, this method always returns
        // an instance of FilterColumnDataTransferObject. Without this
        // assert line, static code analyzers will complain.
        Assert::isInstanceOf($result, FilterColumnDataTransferObject::class);

        return $result;
    }
}
