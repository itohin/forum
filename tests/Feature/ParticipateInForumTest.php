<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ParticipateInForumTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guestsMayNotAddReplies()
    {
        $this->withoutExceptionHandling();
        $this->expectException('Illuminate\Auth\AuthenticationException');

        $this->post('/threads/some-channel/1/replies', []);
    }

    /** @test */
    public function anAuthUserCanParticipateInThreads()
    {
        $this->be($user = factory('App\User')->create());
        $thread = factory('App\Thread')->create();
        $reply = factory('App\Reply')->make();

        $this->post($thread->path(). '/replies', $reply->toArray());

        $this->get($thread->path())
            ->assertSee($reply->body);
    }
}
