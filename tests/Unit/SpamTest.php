<?php

namespace Tests\Unit;

use App\Inspections\Spam;
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

        $this->assertFalse($spam->detect('Some comment'));

        $this->expectException(\Exception::class);

        $spam->detect('yahoo customer support');
    }

    /** @test */
    public function itCheckKeyHeldDown()
    {
        $spam = new Spam();

        $this->expectException(\Exception::class);

        $spam->detect('Helo world aaaaaaaaaaaaaaaa');
    }
}
