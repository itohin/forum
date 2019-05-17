<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateThreadsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guestsMayNotCreateThreads()
    {
        $this->withoutExceptionHandling();
        $this->expectException('Illuminate\Auth\AuthenticationException');
        $thread = make('App\Thread');

        $this->post('/threads', $thread->toArray());
    }

    /** @test */
    public function guestsCannotSeeCreatePage()
    {
        $this->get(route('threads.create'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function authUsersCanCreateThreads()
    {
        $this->signIn();

        $thread = create('App\Thread');

        $this->post('/threads', $thread->toArray());

        $this->get($thread->path())
            ->assertSee($thread->title);
    }
}
