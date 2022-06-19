<?php

declare(strict_types=1);

namespace Pointybeard\FilterableModel;

use Spatie\DataTransferObject\Caster;
use Webmozart\Assert\Assert;

class FilterColumnCollectionCaster implements Caster
{
    public function cast(mixed $data): FilterColumnCollection
    {
        Assert::isArray($data, 'data passed to FilterColumnCollectionCaster:cast() must be an array');

        return new FilterColumnCollection(array_map(
            fn ($name, $value) => new FilterColumnDataTransferObject(name: $name, value: $value),
            array_keys($data),
            array_values($data)
        ));
    }
}
