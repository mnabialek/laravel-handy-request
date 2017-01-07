<?php

namespace Mnabialek\LaravelHandyRequest\Filters\Contracts;

interface FieldFilter extends Filter
{
    /**
     * Apply filter for given value
     *
     * @param mixed $value Value for which filter will be used
     * @param mixed $key Key of input for which this filter will be used
     *
     * @return mixed
     */
    public function apply($value, $key);
}
