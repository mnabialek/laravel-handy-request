<?php

namespace Tests;

use Mockery;
use PHPUnit_Framework_TestCase;

class UnitTestCase extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }
}
