<?php

namespace Mnabialek\LaravelHandyRequest\Filters\Contracts;

interface GlobalFilter extends Filter
{
    /**
     * Apply global filter for input
     *
     * @param array $input
     *
     * @return mixed
     */
    public function applyGlobal(array $input);
}
