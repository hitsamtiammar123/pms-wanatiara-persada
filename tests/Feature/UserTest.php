<?php

namespace Tests\Feature;

use App\Model\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{

    use DatabaseTransactions;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $response = $this->get('/');

        $response->assertSeeInOrder(['PT','Wanatiara']);
    }

    public function testAuthUser(){
        $user_1=factory(User::class)->make();
        $this->actingAs($user_1);
        $user=\Auth::user();

        if($user)
            $this->assertTrue(true);
        else
            $this->assertTrue(false);

    }
}
