<?php

namespace Mnabialek\LaravelHandyRequest;

use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Mnabialek\LaravelHandyRequest\Traits\HandyRequest as HandyRequestTrait;

class HandyRequest extends Request
{
    use HandyRequestTrait;

    /**
     * @var Container
     */
    protected $container;

    /**
     * Override default request
     *
     * @param Container $container
     */
    public static function overrideDefaultRequest($container)
    {
        self::setFactory(function (
            array $query = [],
            array $request = [],
            array $attributes = [],
            array $cookies = [],
            array $files = [],
            array $server = [],
            $content = null) use ($container) {
            $newRequest = new static($query, $request, $attributes, $cookies, $files, $server,
                $content);

            return $newRequest->setContainer($container);
        });
    }

    /**
     * Set the container implementation.
     *
     * @param  Container $container
     *
     * @return $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }
}
