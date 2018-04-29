<?php

use App\Channel;
use App\Thread;
use App\User;
use Faker\Generator as Faker;
use Illuminate\Notifications\DatabaseNotification;
use Ramsey\Uuid\Uuid;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\User::class, function (Faker $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Thread::class, function($faker) {
    return [
        'user_id' => function() {
            return factory(User::class)->create()->id;
        },
        'channel_id' => function()
        {
            return factory(Channel::class)->create()->id;
        },
        'title' => $faker->sentence,
        'body' => $faker->paragraph
    ];
});

$factory->define(Channel::class, function($faker) {
    $name = $faker->word;

    return [
        'name' => $name,
        'slug' => $name
    ];
});


$factory->define(App\Reply::class, function($faker) {
    return [
        'thread_id' => function() {
            return factory(Thread::class)->create()->id;
        },
        'user_id' => function() {
            return factory(User::class)->create()->id;
        },
        'body' => $faker->paragraph
    ];
});

$factory->define(DatabaseNotification::class, function($faker) {
    return [
        'id' => Uuid::uuid4()->toString(),
        'type' => 'App\Notifications\ThreadWasUpdated',
        'notifiable_id' => function() {
            return auth()->id() ?: factory(User::class)->create()->id;
        },
        'notifiable_type' => User::class,
        'data' => ['foo' => 'bar']
    ];
});

