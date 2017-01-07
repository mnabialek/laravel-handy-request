<?php

namespace Mnabialek\LaravelHandyRequest\Filters\Contracts;

interface Filter
{
    /**
     * Whether filter is used on whole input or maybe for single value only
     *
     * @return bool
     */
    public function isGlobal();

    /**
     * Apply filter for given value
     *
     * @param mixed $value Value for which filter will be used
     * @param mixed $key Key of input for which this filter will be used
     *
     * @return mixed
     */
    public function apply($value, $key);

    /**
     * @param array $input
     *
     * @return mixed
     */
    public function applyGlobal(array $input);

    /**
     * Set options for filter
     *
     * @param array $options
     */
    public function setOptions(array $options = []);
}
