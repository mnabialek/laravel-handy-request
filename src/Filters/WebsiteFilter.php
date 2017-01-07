<?php

namespace Mnabialek\LaravelHandyRequest\Filters;

use Mnabialek\LaravelHandyRequest\Filters\Base\FieldFilter;

class WebsiteFilter extends FieldFilter
{
    /**
     * {@inheritdoc}
     */
    public function apply($value, $key)
    {
        if (empty($value)) {
            return $value;
        }

        if (starts_with($value, ['http://', 'https://'])) {
            return $value;
        }

        $secure = (bool)array_get($this->options, 'secure', $this->defaultSecure());

        return (($secure) ? 'https://' : 'http://') . $value;
    }

    /**
     * Whether use https when no protocol given
     *
     * @return bool
     */
    protected function defaultSecure()
    {
        return false;
    }
}
