<?php

declare(strict_types=1);

namespace Pointybeard\FilterableModel;

use Spatie\DataTransferObject\Attributes\Strict;
use Spatie\DataTransferObject\DataTransferObject;

#[Strict]
class FilterColumnDataTransferObject extends DataTransferObject
{
    public string $name;

    public mixed $value;
}
