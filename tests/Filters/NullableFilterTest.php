<?php

namespace Tests\Filters;

use Mnabialek\LaravelHandyRequest\Filters\NullableFilter;
use Tests\UnitTestCase;

class NullableFilterTest extends UnitTestCase
{
    /** @test */
    public function it_shows_its_not_a_global_filter()
    {
        $filter = new NullableFilter();
        $this->assertFalse($filter->isGlobal());
    }

    /** @test */
    public function it_doesnt_change_non_empty_value()
    {
        $filter = new NullableFilter();
        $filter->setOptions([]);
        $this->assertSame(3, $filter->apply(3, 'test'));
    }

    /** @test */
    public function it_does_change_empty_string_into_null()
    {
        $filter = new NullableFilter();
        $filter->setOptions([]);
        $this->assertNull($filter->apply('', 'test'));
    }

    /** @test */
    public function it_does_change_boolean_false_into_null()
    {
        $filter = new NullableFilter();
        $filter->setOptions([]);
        $this->assertNull($filter->apply(false, 'test'));
    }

    /** @test */
    public function it_does_change_integer_0_into_null()
    {
        $filter = new NullableFilter();
        $filter->setOptions([]);
        $this->assertNull($filter->apply(0, 'test'));
    }

    /** @test */
    public function it_does_not_change_not_trimmed_empty_string()
    {
        $filter = new NullableFilter();
        $filter->setOptions([]);
        $this->assertSame(' ', $filter->apply(' ', 'test'));
    }
}
