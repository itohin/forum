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
        $this->signIn();
        $thread = create('App\Thread');
        $reply = make('App\Reply');

        $this->post($thread->path(). '/replies', $reply->toArray());

        $this->assertDatabaseHas('replies', ['body' => $reply->body]);
        $this->assertEquals(1, $thread->fresh()->replies_count);
    }

    /** @test */
    public function replyRequiresABody()
    {
        $this->withExceptionHandling()->signIn();

        $thread = create('App\Thread');
        $reply = make('App\Reply', ['body' => null]);

        $this->post($thread->path(). '/replies', $reply->toArray())
            ->assertSessionHasErrors('body');
    }

    /** @test */
    public function unauthUsersCannotDeleteReplies()
    {
        $this->withExceptionHandling();

        $reply = create('App\Reply');

        $this->delete(route('reply.delete', $reply))
            ->assertRedirect(route('login'));

        $this->signIn()
            ->delete(route('reply.delete', $reply))
            ->assertStatus(403);
    }

    /** @test */
    public function authUsersCanDeleteReplies()
    {
        $this->signIn();
        $reply = create('App\Reply', ['user_id' => auth()->id()]);

        $this->delete(route('reply.delete', $reply));

        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
        $this->assertEquals(0, $reply->thread->fresh()->replies_count);
    }

    /** @test */
    public function unauthUsersCannotUpdateReplies()
    {
        $this->withExceptionHandling();

        $reply = create('App\Reply');

        $this->patch(route('reply.update', $reply))
            ->assertRedirect(route('login'));

        $this->signIn()
            ->patch(route('reply.update', $reply))
            ->assertStatus(403);
    }

    /** @test */
    public function authUsersCanUpdateReplies()
    {
        $this->signIn();
        $reply = create('App\Reply', ['user_id' => auth()->id()]);

        $this->patch(route('reply.update', $reply), ['body' => 'Changed']);

        $this->assertDatabaseHas('replies', ['id' => $reply->id, 'body' => 'Changed']);
    }

    /** @test */
    public function replyWihtSpamMayNotBeCreated()
    {
        $this->withoutExceptionHandling();
        $this->signIn();
        $thread = create('App\Thread');
        $reply = make('App\Reply', [
            'body' => 'Yahoo customer support'
        ]);

        $this->expectException(\Exception::class);

        $this->post($thread->path(). '/replies', $reply->toArray());

    }
}
