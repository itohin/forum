<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FavoritesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guestCannotFavorite()
    {
        $reply = create('App\Reply');

        $this->post(route('favorites.reply', $reply))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function authUserCanFavoriteAnyReply()
    {
        $this->signIn();

        $reply = create('App\Reply');

        $this->post(route('favorites.reply', $reply));

        $this->assertCount(1, $reply->favorites);
    }

    /** @test */
    public function authUserCanUnfavoriteAReply()
    {
        $this->signIn();

        $reply = create('App\Reply');

        $reply->favorite();

        $this->delete(route('favorites.delete', $reply));

        $this->assertCount(0, $reply->favorites);
    }

    /** @test */
    public function authUserCanFavoriteReplyOnlyOnce()
    {
        $this->signIn();

        $reply = create('App\Reply');

        $this->post(route('favorites.reply', $reply));
        $this->post(route('favorites.reply', $reply));

        $this->assertCount(1, $reply->favorites);
    }
}
