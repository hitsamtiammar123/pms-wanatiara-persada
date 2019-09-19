<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{

    //use DatabaseMigrations;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {

        $this->assertTrue(false);
    }

    public function testComparingStrName(){
        $str='冶炼厂产量、品位Volume Produksi dan Kadar Smelter plant ';
        $r=str_name_compare($str,'冶炼厂产量、品位Volume Produksi dan Kadar Smelter plant ');

        $this->assertTrue($r);

    }
}
