<?php

namespace Tests\Filters;

use Mnabialek\LaravelHandyRequest\Filters\TrimFilter;
use Tests\UnitTestCase;

class TrimFilterTest extends UnitTestCase
{
    /** @test */
    public function it_shows_its_not_a_global_filter()
    {
        $filter = new TrimFilter();
        $this->assertFalse($filter->isGlobal());
    }

    /** @test */
    public function it_trims_non_trimmed_string()
    {
        $filter = new TrimFilter();
        $filter->setOptions([]);
        $this->assertSame('3', $filter->apply(' 3 ', 'test'));
    }

    /** @test */
    public function it_returns_original_string_when_its_already_trimmed()
    {
        $filter = new TrimFilter();
        $filter->setOptions([]);
        $this->assertSame('3a3', $filter->apply('3a3', 'test'));
    }

    /** @test */
    public function it_doesnt_trim_boolean_value()
    {
        $filter = new TrimFilter();
        $filter->setOptions([]);
        $this->assertSame(false, $filter->apply(false, 'test'));
    }

    /** @test */
    public function it_doesnt_trim_integer_value()
    {
        $filter = new TrimFilter();
        $filter->setOptions([]);
        $this->assertSame(0, $filter->apply(0, 'test'));
    }
}
