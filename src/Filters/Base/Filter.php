<?php

namespace Mnabialek\LaravelHandyRequest\Filters\Base;

use Mnabialek\LaravelHandyRequest\Filters\Contracts\Filter as FilterContract;

abstract class Filter implements FilterContract
{
    /**
     * @var array
     */
    protected $options;

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options = [])
    {
        $this->options = $options;
    }
}
