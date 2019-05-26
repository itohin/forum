<?php

namespace Tests\Unit;

use App\Spam;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SpamTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itValidatesSpam()
    {
        $spam = new Spam();

        $body = 'Some comment';

        $this->assertFalse($spam->detect($body));
    }
}
