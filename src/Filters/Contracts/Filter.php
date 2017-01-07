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
     * Set options for filter
     *
     * @param array $options
     */
    public function setOptions(array $options = []);
}
