<?php

namespace Mnabialek\LaravelHandyRequest\Filters;

use Mnabialek\LaravelHandyRequest\Filters\Base\FieldFilter;

class NullableFilter extends FieldFilter
{
    /**
     * {@inheritdoc}
     */
    public function apply($value, $key)
    {
        return $value ?: null;
    }
}
