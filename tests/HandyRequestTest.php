<?php

namespace Tests;

use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Mnabialek\LaravelHandyRequest\HandyRequest;
use Mockery;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class HandyRequestTest extends UnitTestCase
{
    /** @test */
    public function it_can_initialize_from_other_request()
    {
        $query = ['query_parameter_1' => 2, 'query_parameter_2' => 3];
        $input = ['a' => 2, 'b' => 'test'];
        $attributes = ['attribute_1' => 'Attribute 1'];
        $cookies = ['sample_cookie' => 'Sample Cookie content'];
        $files = [
            'sample_file' => new UploadedFile(__FILE__, 'abc.jpg', 'text/html', 200,
                null, true),
        ];
        $server = ['server_1' => 'Server 1 value', 'server_2' => 'Server 2 value'];

        $request = new Request($query, $input, $attributes, $cookies, $files, $server, null);

        $session = Mockery::mock(SessionInterface::class);
        $session->shouldReceive('foo')->andReturn('bar');
        $request->setSession($session);

        $handyRequest = new HandyRequest();
        $handyRequest->initializeFromRequest($request);

        $this->assertEquals($request->query(), $handyRequest->query());
        $this->assertEquals($request->input(), $handyRequest->input());
        $this->assertEquals($request->cookie(), $handyRequest->cookie());
        $this->assertEquals($request->file(), $handyRequest->file());
        $this->assertEquals($request->server(), $handyRequest->server());
        $this->assertSame('bar', $handyRequest->session()->foo());
    }

    /** @test */
    public function it_can_set_container()
    {
        $handyRequest = new class() extends HandyRequest {
            public function getContainer()
            {
                return $this->container;
            }
        };
        $app = Mockery::mock(Container::class);
        $app->shouldReceive('foo')->andReturn('bar');
        $handyRequest->setContainer($app);

        $this->assertSame('bar', $handyRequest->getContainer()->foo());
    }
}
