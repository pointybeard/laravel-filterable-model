<?php

declare(strict_types=1);

namespace Pointybeard\FilterableModel;

use Illuminate\Database\Eloquent\Model;
use Pointybeard\FilterableModel\Contracts\FilterableModelInterface;

abstract class AbstractFilterableModel extends Model implements FilterableModelInterface
{
}
