<?php declare(strict_types=1);

namespace Tests\Feature;

use App\Thread;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

/**
 * Class CreateThreadsTest.
 */
class CreateThreadsTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function guest_may_not_create_threads()
    {
        $this->expectException('Illuminate\Auth\AuthenticationException');

        $thread = make(Thread::class);

        $this->post('/threads', $thread->toArray());
    }

    /** @test */
    public function an_authenticated_user_can_create_new_form_threads()
    {
//        $this->actingAs(create(User::class));
        $this->signIn();

        $thread = make(Thread::class);

        $this->post('/threads', $thread->toArray());

        $this->get($thread->path())
            ->assertSee($thread->title)
            ->assertSee($thread->body);
    }
}
