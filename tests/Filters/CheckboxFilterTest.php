<?php

use Mnabialek\LaravelHandyRequest\Filters\CheckboxFilter;
use Tests\UnitTestCase;

class CheckboxFilterTest extends UnitTestCase
{
    /** @test */
    public function it_shows_its_a_global_filter()
    {
        $filter = new CheckboxFilter();
        $this->assertTrue($filter->isGlobal());
    }

    /** @test */
    public function it_sets_valid_values_without_value_defined_for_flat_structure()
    {
        $filter = new CheckboxFilter();
        $filter->setOptions([
            'only' => ['a', 'b', 'c', 'd', 'e', 'f'],
        ]);
        $this->assertEquals([
            'a' => 0,
            'b' => false,
            'c' => 1,
            'd' => true,
            'e' => 0,
            'f' => 0,
            'g' => 'something',
        ], $filter->applyGlobal([
            'a' => 0,
            'b' => false,
            'c' => 1,
            'd' => true,
            'g' => 'something',
        ]));
    }

    /** @test */
    public function it_sets_valid_values_with_value_defined_for_flat_structure()
    {
        $filter = new CheckboxFilter();
        $filter->setOptions([
            'only' => ['a', 'b', 'c', 'd', 'e', 'f'],
            'value' => 3,
        ]);
        $this->assertEquals([
            'a' => 0,
            'b' => false,
            'c' => 1,
            'd' => true,
            'e' => 3,
            'f' => 3,
            'g' => 'something',
        ], $filter->applyGlobal([
            'a' => 0,
            'b' => false,
            'c' => 1,
            'd' => true,
            'g' => 'something',
        ]));
    }
}
