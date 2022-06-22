<?php

declare(strict_types=1);

namespace Pointybeard\FilterableModel;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\DataTransferObject;
use Webmozart\Assert\Assert;

class Filter extends DataTransferObject
{
    public ?string $sortBy;

    public ?string $sortOrder;

    #[CastWith(FilterColumnCollectionCaster::class)]
    public FilterColumnCollection $filters;

    // Making the constructor final ensures that the child class cannot overload the constructor which
    // would result in an unsafe new static() error in fromRequest() during static code analysis
    // (see, https://phpstan.org/blog/solving-phpstan-error-unsafe-usage-of-new-static)
    /**
     * @param array<mixed> ...$args
     */
    final public function __construct(...$args)
    {
        parent::__construct(...$args);
    }

    public static function fromRequest(Request $request): self
    {
        $query = $request->query();

        // Request::query() will return array, string, or null. We
        // only every want to deal with arrays. This will wrap
        // a string or null into an array.
        if (false == is_array($query)) {
            $query = [$query];
        }

        return new static(
            filters: array_filter( // remove anything that isn't a string
                $query,
                fn ($value) => is_string($value)
            ),
            sortBy: $query['sort']['by'] ?? null,
            sortOrder: $query['sort']['order'] ?? null
        );
    }

    /**
     * @param array<int, mixed> $args
     */
    public function __call(string $name, array $args): Builder
    {
        [$builder, $model, $value] = $args;

        Assert::isInstanceOf($builder, Builder::class);
        Assert::isInstanceOf($model, AbstractFilterableModel::class);
        Assert::string($value);

        return $builder->where($name, $value);
    }

    protected function sort(Builder $builder, AbstractFilterableModel $model, string $by, string $order): Builder
    {
        // (guard) column doesn't exist
        if (false == Schema::hasColumn($model->getTable(), $by)) {
            $by = $model->getSortByDefault();
        }

        // (guard) order value invalid
        if (false == in_array(strtolower($order), ['asc', 'desc'], true)) {
            $order = $model->getSortOrderDefault();
        }

        return $builder->orderBy($by, $order);
    }

    public function apply(AbstractFilterableModel $model, Builder $builder): Builder
    {
        foreach ($this->filters as $f) {
            if (false == $f instanceof FilterColumnDataTransferObject) {
                continue;
            }

            if ('sort' != $f->name && true == in_array($f->name, $model->getFilterable(), true) && true == Schema::hasColumn($model->getTable(), $f->name)) {
                $callable = [$this, $f->name];

                // (guard)
                if (false == is_callable($callable)) {
                    continue;
                }

                call_user_func_array($callable, [
                    $builder,
                    $model,
                    $f->value,
                ]);
            }
        }

        if (true == $model->getSortable()) {
            $this->sort(
                $builder,
                $model,
                $this->sortBy ?? $model->getSortByDefault(),
                $this->sortOrder ?? $model->getSortOrderDefault()
            );
        }

        return $builder;
    }
}
