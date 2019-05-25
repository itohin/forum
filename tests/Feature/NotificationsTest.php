<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function notificationPreparedWhenThreadReceivesNewReplyNotByCurrentUser()
    {
        $this->signIn();

        $thread = create('App\Thread')->subscribe();

        $this->assertCount(0, auth()->user()->notifications);

        $thread->addReply([
            'user_id' => auth()->id(),
            'body' => 'Some reply'
        ]);

        $this->assertCount(0, auth()->user()->fresh()->notifications);

        $thread->addReply([
            'user_id' => create('App\User')->id,
            'body' => 'Some reply'
        ]);

        $this->assertCount(1, auth()->user()->fresh()->notifications);
        
    }

    /** @test */
    function userCanGetUnreadNotifications()
    {
        $this->signIn();

        $thread = create('App\Thread')->subscribe();

        $thread->addReply([
            'user_id' => create('App\User')->id,
            'body' => 'Some reply'
        ]);

        $response = $this->getJson(route('notifications.index', [auth()->user()]))->json();

        $this->assertCount(1, $response);
    }

    /** @test */
    function userCanMarkNotificationsAsRead()
    {
        $this->signIn();

        $thread = create('App\Thread')->subscribe();

        $thread->addReply([
            'user_id' => create('App\User')->id,
            'body' => 'Some reply'
        ]);

        $this->assertCount(1, auth()->user()->unreadNotifications);

        $notification = auth()->user()->unreadNotifications->first();

        $this->delete(route('notifications.delete', [auth()->user(), $notification]));

        $this->assertCount(0, auth()->user()->fresh()->unreadNotifications);
    }
}
