<?php

declare(strict_types=1);

namespace Pointybeard\FilterableModel;

use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Castable;
use Spatie\LaravelData\Support\DataProperty;
use Webmozart\Assert\Assert;

class FilterColumnCollectionCaster implements Castable
{
    public function __construct(public string $value)
    {
        //
    }

    /**
     * @param  array<mixed>  ...$arguments
     */
    public static function dataCastUsing(...$arguments): Cast
    {
        return new class implements Cast
        {
            /**
             * @param  array<mixed>  $context
             */
            public function cast(DataProperty $property, mixed $data, array $context): mixed
            {
                Assert::isArray($data, 'data passed to FilterColumnCollectionCaster must be an array');

                return new FilterColumnCollection(array_map(
                    fn ($name, $value) => new FilterColumnData(name: $name, value: $value),
                    array_keys($data),
                    array_values($data)
                ));
            }
        };
    }
}
