<?php

namespace Mnabialek\LaravelHandyRequest\Filters\Base;

use Mnabialek\LaravelHandyRequest\Filters\Contracts\GlobalFilter as GlobalFilterContract;

abstract class GlobalFilter extends Filter implements GlobalFilterContract
{
    /**
     * {@inheritdoc}
     */
    public function isGlobal()
    {
        return true;
    }
}
