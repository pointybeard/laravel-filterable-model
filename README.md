# Laravel Filterable Model

Add filtering via http request query params to Eloquent models in Laravel

## Installation
```bash
> composer require pointybeard/laravel-filterable-model
```

## Usage
Extend your Eloquent models with `Pointybeard\FilterableModel\AbstractFilterableModel` and then use the trait `Pointybeard\FilterableModel\Traits\FilterableTrait`. This will add a new scope called `filter`, as well as the following methods, to your model:

- `getFilterable()`
- `getSortable()`
- `getSortByDefault()`
- `getSortOrderDefault()`

Then, add protected properties `$filterable`, `$sortable`, `$sortByDefault`, and `$sortOrderDefault` to control the behaviour of filtering. E.g.

```php
protected $filterable = ['title', 'category', 'tags', 'is_published'];
protected bool $sortable = true;
protected string $sortByDefault = 'published_at'; // default is 'created_at'
protected string $sortOrderDefault = 'desc'; // default is 'asc'
```

Finally, you can use the `->filter()` scope to filter results by passing an instance of `Filter`. E.g.

```php
use App\Models\MyModel;
use Pointybeard\FilterableModel\Filter;

MyModel::filter(new Filter(
    filters: [
        'tag' => 'article',
        'is_published' => 1,
    ],
));
```

Alternatively, use the `Filter::fromRequest()` method to build a filter from request query params, e.g. in your controllers like so:

```php

use App\Models\MyModel;
use Pointybeard\FilterableModel\Filter;

return response()->json(
    MyModel::filter(Filter::fromRequest($request))->get(),
    Response::HTTP_OK
);
```

By default, all comparisons are done by injecting an equals (`=`) where clause into the database calls. To use other comparisons, or add more complex logic, create a new class that extends `Pointybeard\FilterableModel\Filter` and add your own methods. For example, to use a `LIKE` comparison for your `tag` field and a boolean comparison for `is_published`:

```php
use Pointybeard\FilterableModel\AbstractFilterableModel;
use Pointybeard\FilterableModel\Filter;
use Illuminate\Database\Eloquent\Builder;

Class MyModelFilter extends Filter
{
    public function tag(Builder $builder, AbstractFilterableModel $model, string $value): Builder
    {
        return $builder->where('tag', 'like', "%{$value}%");
    }

    public function is_published(Builder $builder, AbstractFilterableModel $model, string $value): Builder
    {
        // Convert a string representation of true/false into an actual boolean
        $value = in_array(strtolower($value), ['1', 'true', 'yes']) ? true : false;

        return $builder->where('protected', $value);
    }
}
```

## Contributing
We encourage you to contribute to this project. Please check out the [Contributing documentation][doc-CONTRIBUTING] for guidelines about how to get involved.

## Support
If you believe you have found a bug, please report it using the [GitHub issue tracker][ext-issues].

## Authors
- [Alannah Kearney](https://github.com/pointybeard)
- [All Contributors][ext-contributors]

## License
"Laravel Filterable Model" is released under the MIT License. See [LICENCE][doc-LICENCE] for details.

[doc-LICENCE]: http://www.opensource.org/licenses/MIT
[doc-CONTRIBUTING]: https://github.com/pointybeard/laravel-filterable-model/blob/master/CONTRIBUTING.md
[ext-issues]: https://github.com/pointybeard/laravel-filterable-model/issues
[ext-contributors]: https://github.com/pointybeard/laravel-filterable-model/contributors
