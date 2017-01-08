<?php

namespace Tests\Filters;

use Mnabialek\LaravelHandyRequest\Filters\SecureWebsiteFilter;
use Tests\UnitTestCase;

class SecureWebsiteFilterTest extends UnitTestCase
{
    /**
     * @var SecureWebsiteFilter
     */
    protected $filter;

    protected function setUp()
    {
        parent::setUp();
        $this->filter = new SecureWebsiteFilter();
        $this->filter->setOptions([]);
    }

    /** @test */
    public function it_shows_its_not_a_global_filter()
    {
        $this->assertFalse($this->filter->isGlobal());
    }

    /** @test */
    public function it_doesnt_modify_valid_http_website()
    {
        $this->assertSame('http://example.com/test',
            $this->filter->apply('http://example.com/test', 'test'));
    }

    /** @test */
    public function it_doesnt_modify_valid_https_website()
    {
        $this->assertSame('https://example.com/test',
            $this->filter->apply('https://example.com/test', 'test'));
    }

    /** @test */
    public function it_adds_https_to_website()
    {
        $this->assertSame('https://example.com/test',
            $this->filter->apply('example.com/test', 'test'));
    }

    /** @test */
    public function it_adds_https_to_not_trimmed_website()
    {
        $this->assertSame('https://example.com/test  ',
            $this->filter->apply('  example.com/test  ', 'test'));
    }

    /** @test */
    public function it_adds_http_to_website_when_option_is_set_to_non_secure()
    {
        $this->filter->setOptions(['secure' => false]);
        $this->assertSame('http://example.com/test',
            $this->filter->apply('example.com/test', 'test'));
    }
}
