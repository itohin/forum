<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function  userHasProfile()
    {
        $user = create('App\User');

        $this->get('/profiles/' . $user->name)
            ->assertSee($user->name);
    }

    /** @test */
    public function  profileShowAllThreadsByUser()
    {
        $user = create('App\User');

        $thread = create('App\Thread', ['user_id' => $user->id]);

        $this->get('/profiles/' . $user->name)
            ->assertSee($thread->title)
            ->assertSee($thread->body);
    }
}
