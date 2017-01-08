<?php

namespace Tests\Filters;

use Mnabialek\LaravelHandyRequest\Filters\NullableFilter;
use Tests\UnitTestCase;

class NullableFilterTest extends UnitTestCase
{
    /**
     * @var NullableFilter
     */
    protected $filter;

    protected function setUp()
    {
        parent::setUp();
        $this->filter = new NullableFilter();
        $this->filter->setOptions([]);
    }

    /** @test */
    public function it_shows_its_not_a_global_filter()
    {
        $this->assertFalse($this->filter->isGlobal());
    }

    /** @test */
    public function it_doesnt_change_non_empty_value()
    {
        $this->assertSame(3, $this->filter->apply(3, 'test'));
    }

    /** @test */
    public function it_does_change_empty_string_into_null()
    {
        $this->assertNull($this->filter->apply('', 'test'));
    }

    /** @test */
    public function it_does_change_boolean_false_into_null()
    {
        $this->assertNull($this->filter->apply(false, 'test'));
    }

    /** @test */
    public function it_does_change_integer_0_into_null()
    {
        $this->assertNull($this->filter->apply(0, 'test'));
    }

    /** @test */
    public function it_does_not_change_not_trimmed_empty_string()
    {
        $this->assertSame(' ', $this->filter->apply(' ', 'test'));
    }
}
