<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Thread;
use App\User;
use Faker\Generator as Faker;

$factory->define(Thread::class, function (Faker $faker) {
    return [
        'user_id' => factory(User::class),
        'title' => $faker->sentence,
        'body' => $faker->paragraph,
    ];
});
