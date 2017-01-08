<?php

namespace Tests\Filters;

use Mnabialek\LaravelHandyRequest\Filters\TrimFilter;
use Tests\UnitTestCase;

class TrimFilterTest extends UnitTestCase
{
    /**
     * @var TrimFilter
     */
    protected $filter;

    protected function setUp()
    {
        parent::setUp();
        $this->filter = new TrimFilter();
        $this->filter->setOptions([]);
    }

    /** @test */
    public function it_shows_its_not_a_global_filter()
    {
        $this->assertFalse($this->filter->isGlobal());
    }

    /** @test */
    public function it_trims_non_trimmed_string()
    {
        $this->assertSame('3', $this->filter->apply(' 3 ', 'test'));
    }

    /** @test */
    public function it_returns_original_string_when_its_already_trimmed()
    {
        $this->assertSame('3a3', $this->filter->apply('3a3', 'test'));
    }

    /** @test */
    public function it_doesnt_trim_boolean_value()
    {
        $this->assertSame(false, $this->filter->apply(false, 'test'));
    }

    /** @test */
    public function it_doesnt_trim_integer_value()
    {
        $this->assertSame(0, $this->filter->apply(0, 'test'));
    }
}
