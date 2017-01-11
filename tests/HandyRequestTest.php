<?php

namespace Tests;

use Illuminate\Container\Container;
use Illuminate\Foundation\Application;
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
        $input = ['a' => 2, 'b' => 'test'];
        $cookies = ['sample_cookie' => 'Sample Cookie content'];
        $files = [
            'sample_file' => new UploadedFile(__FILE__, 'abc.jpg', 'text/html', 200,
                null, true),
        ];
        $server = ['server_1' => 'Server 1 value', 'server_2' => 'Server 2 value'];

        HandyRequest::overrideDefaultRequest(new Application());

        $handyRequest = Request::create('http://example.com', 'GET', $input, $cookies,
            $files, $server, null);
        $this->assertTrue($handyRequest instanceof HandyRequest);

        $session = Mockery::mock(SessionInterface::class);
        $session->shouldReceive('foo')->andReturn('bar');
        $handyRequest->setSession($session);

        $this->assertEquals($input, $handyRequest->query());
        $this->assertEquals($input, $handyRequest->input());
        $this->assertEquals($cookies, $handyRequest->cookie());
        $this->assertEquals($files, $handyRequest->file());
        $this->assertArraySubset($server, $handyRequest->server());
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
