<?php

namespace Tests\Traits;

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Mnabialek\LaravelHandyRequest\HandyRequest;
use Tests\UnitTestCase;

class HandyRequestTest extends UnitTestCase
{
    /** @test */
    public function it_modifies_input_using_trim_filters()
    {
        $input = ['a' => ' aaa ', 'b' => ' bbb ', 'c' => 2];

        $request = $this->initializeRequest($input, new class() extends HandyRequest {
            protected $filters = [
                'trim',
            ];
        });

        $this->assertEquals(['a' => 'aaa', 'b' => 'bbb', 'c' => 2], $request->all());
        $this->assertEquals(['a' => 'aaa', 'b' => 'bbb', 'c' => 2], $request->input());
        $this->assertEquals('aaa', $request->input('a'));
        $this->assertEquals(['a' => ' aaa ', 'b' => ' bbb ', 'c' => 2], $request->original());
        $this->assertEquals(' aaa ', $request->original('a'));
        $this->assertEquals(['a' => 'aaa', 'b' => 'bbb', 'c' => 2], $request->filtered());
        $this->assertEquals('aaa', $request->filtered('a'));
        $this->assertEquals(['a' => 'aaa', 'c' => 2], $request->only('a', 'c'));
        $this->assertEquals(['b' => 'bbb', 'c' => 2], $request->except('a'));
    }

    /** @test */
    public function it_doesnt_modify_input_when_overriding_input_method_using_trim_filters()
    {
        $input = ['a' => ' aaa ', 'b' => ' bbb ', 'c' => 2];

        $request = $this->initializeRequest($input, new class() extends HandyRequest {
            protected $filters = [
                'trim',
            ];

            public function input($key = null, $default = null)
            {
                return parent::original($key, null);
            }
        });

        $this->assertEquals(['a' => ' aaa ', 'b' => ' bbb ', 'c' => 2], $request->all());
        $this->assertEquals(['a' => ' aaa ', 'b' => ' bbb ', 'c' => 2], $request->input());
        $this->assertEquals(' aaa ', $request->input('a'));
        $this->assertEquals(['a' => ' aaa ', 'b' => ' bbb ', 'c' => 2], $request->original());
        $this->assertEquals(' aaa ', $request->original('a'));
        $this->assertEquals(['a' => 'aaa', 'b' => 'bbb', 'c' => 2], $request->filtered());
        $this->assertEquals('aaa', $request->filtered('a'));
        $this->assertEquals(['a' => ' aaa ', 'c' => 2], $request->only('a', 'c'));
        $this->assertEquals(['b' => ' bbb ', 'c' => 2], $request->except('a'));
    }

    protected function initializeRequest(array $input, $class)
    {
        $request = new Request($input);

        $handyRequest = new $class();
        $handyRequest->initializeFromRequest($request);
        $handyRequest->setContainer(new Application());

        return $handyRequest;
    }
}
