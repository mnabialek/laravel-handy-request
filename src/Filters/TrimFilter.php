<?php

namespace Mnabialek\LaravelHandyRequest\Filters;

use Mnabialek\LaravelHandyRequest\Filters\Base\FieldFilter;

class TrimFilter extends FieldFilter
{
    /**
     * {@inheritdoc}
     */
    public function apply($value, $key)
    {
        return is_string($value) ? trim($value) : $value;
    }
}
