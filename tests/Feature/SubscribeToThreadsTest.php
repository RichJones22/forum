<?php declare(strict_types=1);

namespace Tests\Feature;

use App\Thread;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class SubscribeToThreadsTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_user_can_subscribe_to_threads()
    {
        $this->signIn();

        // Given we have a thread
        /** @var Thread $thread */
        $thread = create(Thread::class);

        // And we subscribe to the thread
        $this->post($thread->path().'/subscriptions');

        // then, each time a new a reply is left
        $thread->addReply([
           'user_id' => auth()->id(),
           'body' => 'Some reply here.',
        ]);

        $this->assertCount(1, auth()->user()->notifications);
    }

    /** @test */
    public function a_user_can_unsubscribe_from_threads()
    {
        $this->signIn();

        // Given we have a thread
        /** @var Thread $thread */
        $thread = create(Thread::class);

        $thread->subscribe();

        // And we subscribe to the thread
        $this->delete($thread->path().'/subscriptions');

        $this->assertCount(0, $thread->subscriptions()->get());
    }
}
