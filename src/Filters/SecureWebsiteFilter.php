<?php

namespace Mnabialek\LaravelHandyRequest\Filters;

class SecureWebsiteFilter extends WebsiteFilter
{
    /**
     * Whether use https when no protocol given
     *
     * @return bool
     */
    protected function defaultSecure()
    {
        return true;
    }
}
