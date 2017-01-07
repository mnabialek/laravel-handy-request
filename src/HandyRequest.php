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
     * Initialize class from other Request class
     *
     * @param Request $request
     *
     * @return $this
     */
    public function initializeFromRequest(Request $request)
    {
        $files = $request->files->all();

        $files = is_array($files) ? array_filter($files) : $files;

        $this->initialize(
            $request->query->all(), $request->request->all(), $request->attributes->all(),
            $request->cookies->all(), $files, $request->server->all(), $request->getContent()
        );

        if ($session = $request->getSession()) {
            $this->setSession($session);
        }

        $this->setUserResolver($request->getUserResolver());

        $this->setRouteResolver($request->getRouteResolver());

        return $this;
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
