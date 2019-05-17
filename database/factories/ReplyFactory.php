<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Reply;
use App\Thread;
use App\User;
use Faker\Generator as Faker;

$factory->define(Reply::class, function (Faker $faker) {
    return [
        'thread_id' => factory(Thread::class),
        'user_id' => factory(User::class),
        'body' => $faker->paragraph
    ];
});
