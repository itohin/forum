<?php

namespace Tests\Feature;

use Illuminate\Notifications\DatabaseNotification;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->signIn();
    }

    /** @test */
    public function notificationPreparedWhenThreadReceivesNewReplyNotByCurrentUser()
    {
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
        create(DatabaseNotification::class);

        $this->assertCount(1, $this->getJson(route('notifications.index', [auth()->user()]))->json());
    }

    /** @test */
    function userCanMarkNotificationsAsRead()
    {
        create(DatabaseNotification::class);

        $this->assertCount(1, auth()->user()->unreadNotifications);

        $notification = auth()->user()->unreadNotifications->first();

        $this->delete(route('notifications.delete', [auth()->user(), $notification]));

        $this->assertCount(0, auth()->user()->fresh()->unreadNotifications);
    }
}
