<?php

namespace Tests\Feature;

use App\Activity;
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

        $thread = make('App\Thread');

        $response = $this->post('/threads', $thread->toArray());

        $this->get($response->headers->get('Location'))
            ->assertSee($thread->title)
            ->assertSee($thread->body);
    }

    /** @test */
    public function aThreadRequiresATitle()
    {
        $this->publishThreads(['title' => null])
            ->assertSessionHasErrors('title');
    }

    /** @test */
    public function aThreadRequiresABody()
    {
        $this->publishThreads(['body' => null])
            ->assertSessionHasErrors('body');
    }

    /** @test */
    public function unauthorizedUsersCannotDeleteThreads()
    {
        $this->withExceptionHandling();

        $thread = create('App\Thread');

        $this->delete($thread->path())
            ->assertRedirect(route('login'));

        $this->signIn();
        $this->delete($thread->path())
            ->assertStatus(403);
    }

    /** @test */
    public function threadCanBeDeletedByAuthUser()
    {
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);
        $reply = create('App\Reply', ['thread_id' => $thread->id]);

        $response = $this->json('DELETE', $thread->path());

        $response->assertStatus(204);

        $this->assertDatabaseMissing('threads', ['id' => $thread->id]);
        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
        $this->assertEquals(0, Activity::count());
    }

    /** @test */
    public function aThreadRequiresAValidChannel()
    {
        factory('App\Channel', 2)->create();

        $this->publishThreads(['channel_id' => null])
            ->assertSessionHasErrors('channel_id');

        $this->publishThreads(['channel_id' => 999])
            ->assertSessionHasErrors('channel_id');
    }

    public function publishThreads($overrides = [])
    {
        $this->withExceptionHandling()->signIn();

        $thread = make('App\Thread', $overrides);

        return $this->post('/threads', $thread->toArray());
    }
}
