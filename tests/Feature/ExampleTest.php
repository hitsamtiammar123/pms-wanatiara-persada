<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $response = $this->get('/');

        $response->assertSeeInOrder(['PT','Wanatiara Hehe']);
    }

    public function testAuthUser(){
        $user=\Auth::user();

        if($user)
            $this->assertTrue(true);
        else
            $this->assertTrue(false);

    }
}
