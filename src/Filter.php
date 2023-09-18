<?php

declare(strict_types=1);

namespace Pointybeard\FilterableModel;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Spatie\LaravelData\Attributes\WithCastable;
use Spatie\LaravelData\Data;
use Webmozart\Assert\Assert;

class Filter extends Data
{
    public function __construct(
        public ?string $sortBy,

        public ?string $sortOrder,

        #[WithCastable(FilterColumnCollectionCaster::class)]
        public FilterColumnCollection $filters
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        $query = $request->query();

        // Request::query() will return array, string, or null. We
        // only every want to deal with arrays. This will wrap
        // a string or null into an array.
        if (is_array($query) == false) {
            $query = [$query];
        }

        return self::from([
            'filters' => array_filter( // remove anything that isn't a string
                $query,
                fn ($value) => is_string($value)
            ),
            'sortBy' => $query['sort']['by'] ?? null,
            'sortOrder' => $query['sort']['order'] ?? null,
        ]);
    }

    /**
     * @param  array<int, mixed>  $args
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
        if (Schema::hasColumn($model->getTable(), $by) == false) {
            $by = $model->getSortByDefault();
        }

        // (guard) order value invalid
        if (in_array(strtolower($order), ['asc', 'desc'], true) == false) {
            $order = $model->getSortOrderDefault();
        }

        return $builder->orderBy($by, $order);
    }

    public function apply(AbstractFilterableModel $model, Builder $builder): Builder
    {
        foreach ($this->filters as $f) {
            if ($f instanceof FilterColumnData == false) {
                continue;
            }

            if ($f->name != 'sort' && in_array($f->name, $model->getFilterable(), true) == true && Schema::hasColumn($model->getTable(), $f->name) == true) {
                $callable = [$this, $f->name];

                // (guard)
                if (is_callable($callable) == false) {
                    continue;
                }

                call_user_func_array($callable, [
                    $builder,
                    $model,
                    $f->value,
                ]);
            }
        }

        if ($model->getSortable() == true) {
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
