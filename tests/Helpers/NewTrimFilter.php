<?php

namespace Tests\Helpers;

use Mnabialek\LaravelHandyRequest\Filters\Base\FieldFilter;

class NewTrimFilter extends FieldFilter
{
    public function apply($value, $key)
    {
        return substr($value, 0, -1) . ' trimmed';
    }
}
