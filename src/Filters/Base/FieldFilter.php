<?php

namespace Mnabialek\LaravelHandyRequest\Filters\Base;

use Mnabialek\LaravelHandyRequest\Filters\Contracts\FieldFilter as FieldFilterContract;

abstract class FieldFilter extends Filter implements FieldFilterContract
{
    /**
     * {@inheritdoc}
     */
    public function isGlobal()
    {
        return false;
    }
}
