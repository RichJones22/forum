<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Thread;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

/**
 * Class ProfilesTest.
 */
class ProfilesTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_user_has_a_profile()
    {
        $this->signIn();

        create(Thread::class, ['user_id' => auth()->id()]);

        $user = (new User())->newQuery()->where(['id' => auth()->id()])->first();

        $this->get('/profiles/'.auth()->user()->name)
            ->assertSee($user->name);
    }

    /** @test */
    public function profiles_display_all_threads_created_by_associated_user()
    {
        $this->signIn();

        $thread = create(Thread::class, ['user_id' => auth()->id()]);

        $this->get('/profiles/'.auth()->user()->name)
            ->assertSee($thread->title)
            ->assertSee($thread->body);
    }
}
