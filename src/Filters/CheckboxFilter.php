<?php

namespace Mnabialek\LaravelHandyRequest\Filters;

use Mnabialek\LaravelHandyRequest\Filters\Base\GlobalFilter;

class CheckboxFilter extends GlobalFilter
{
    /**
     * {@inheritdoc}
     */
    public function applyGlobal(array $input)
    {
        foreach (array_get($this->options, 'only', []) as $field) {
            array_set($input, $field, array_get($input, $field, $this->defaultValue()));
        }

        return $input;
    }

    /**
     * Get default value assigned to field
     *
     * @return mixed
     */
    protected function defaultValue()
    {
        return array_get($this->options, 'value', 0);
    }
}
