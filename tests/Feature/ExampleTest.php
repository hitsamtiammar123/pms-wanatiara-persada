<?php

namespace Tests\Feature;

use App\Model\User;
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
        $user_1=User::first();
        $this->actingAs($user_1);
        $user=\Auth::user();
        if($user)
            $this->assertTrue(true);
        else
            $this->assertTrue(false);

    }
}
