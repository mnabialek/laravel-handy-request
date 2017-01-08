<?php

namespace Tests\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Mnabialek\LaravelHandyRequest\HandyRequest;
use Mnabialek\LaravelHandyRequest\Providers\HandyRequestServiceProvider;
use Mockery;
use Tests\UnitTestCase;

class HandyRequestServiceProviderTest extends UnitTestCase
{
    /** @test */
    public function it_runs_valid_method_when_running_register_method()
    {
        $app = Mockery::mock(Application::class)->makePartial();
        $app->shouldReceive('foo')->andReturn('bar');
        $provider = new HandyRequestServiceProvider($app);
        $app->shouldReceive('resolving')->once()->passthru();
        $provider->register();

        /** @var Request $request */
        $request = $app->make(Request::class);
        $request->initialize(['a' => 'b']);

        $app->shouldReceive('make')->with('request')->once()->andReturn($request);

        $handyRequest = $app->make(HandyRequest::class);
        $this->assertSame(['a' => 'b'], $handyRequest->query());
    }
}
