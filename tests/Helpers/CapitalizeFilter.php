<?php

namespace Tests\Helpers;

use Mnabialek\LaravelHandyRequest\Filters\Base\FieldFilter;

class CapitalizeFilter extends FieldFilter
{
    public function apply($value, $key)
    {
        return strtoupper($value);
    }
}
