<?php declare(strict_types=1);

namespace Tests\Feature;

use App\Thread;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

/**
 * Class ProfilesTest
 * @package Tests\Feature
 */
class ProfilesTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_user_has_a_profile()
    {
        $user = create(User::class);

        $this->get("/profiles/$user->name")
            ->assertSee($user->name);
    }

    /** @test */
    public function profiles_display_all_threads_created_by_associated_user()
    {
        $user = create(User::class);

        $thread = create(Thread::class, ['user_id' => $user->id]);

        $this->get("/profiles/$user->name")
            ->assertSee($thread->title)
            ->assertSee($thread->body);
    }
}
