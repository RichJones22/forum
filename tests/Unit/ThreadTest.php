<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Channel;
use App\Notifications\ThreadWasUpdated;
use App\Thread;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Testing\Fakes\NotificationFake;
use Tests\TestCase;

/**
 * Class ThreadTest.
 */
class ThreadTest extends TestCase
{
    use DatabaseMigrations;

    /** @var Thread */
    protected $thread;

    public function setUp()
    {
        parent::setUp();

        $this->setThread(create(Thread::class));
    }

    /** @test */
    public function a_thread_can_make_a_string_path()
    {
        $thread = create(Thread::class);

        $this->assertSame(
            '/threads/'.
            $thread->channel->slug.'/'.
            $thread->id, $thread->path()
        );
    }

    /** @test */
    public function a_thread_as_a_creator()
    {
        $this->assertInstanceOf(user::class, $this->getThread()->creator);
    }

    /** @test */
    public function a_thread_has_replies()
    {
        $this->assertInstanceOf(Collection::class, $this->getThread()->replies);
    }

    /** @test */
    public function a_thread_can_add_a_reply()
    {
        $this->thread->addReply([
            'body' => 'foobar',
            'user_id' => 1,
        ]);

        $this->assertCount(1, $this->getThread()->replies);
    }

    /** @test */
    public function a_thread_notifies_all_registered_subscribers_when_a_reply_is_added()
    {
        /** @var NotificationFake $fake */
        $fake = Notification::fake();

        $this->signIn();

        $this->thread->subscribe();

        // reply needs to be from a different user
        $this->thread->addReply([
            'body' => 'foobar',
            'user_id' => create(User::class)->id,
        ]);

        $fake->assertSentTo(auth()->user(), ThreadWasUpdated::class);
    }

    /** @test */
    public function a_thread_belongs_to_a_channel()
    {
        $thread = create(Thread::class);

        $this->assertInstanceOf(Channel::class, $thread->channel);
    }

    /** @test */
    public function a_thread_can_be_subscribed_to()
    {
        /** @var Thread $thread */
        $thread = create(Thread::class);

        // when the user subscribes to the thread.
        $thread->subscribe($userId = 1);

        // then we should fetch all threads that the user has subscribed to.
        $this->assertSame(
            1,
            $thread->subscriptions()->where('user_id', $userId)->count()
        );
    }

    /** @test */
    public function a_thread_can_be_un_subscribed_from()
    {
        /** @var Thread $thread */
        $thread = create(Thread::class);

        // and the user is subscribed to the thread.
        $thread->subscribe($userId = 1);

        $thread->unsubscribe($userId);

        $this->assertCount(0, $thread->subscriptions()->get());
    }

    /** @test */
    public function it_knows_if_the_authenticated_user_is_subscribed_to_it()
    {
        /** @var Thread $thread */
        $thread = create(Thread::class);

        // sign in a user
        $this->signIn();

        // before test; should be false
        $this->assertFalse($thread->isSubscribedTo);

        // and the user is subscribed to the thread.
        $thread->subscribe();

        $this->assertTrue($thread->isSubscribedTo);
    }

    /** @test
     * @throws \Exception
     */
    public function a_thread_can_check_if_the_authenticated_user_has_read_all_replies()
    {
        $this->signIn();

        /** @var Thread $thread */
        $thread = create(Thread::class);

        /** @var User $user */
        $user = auth()->user();

        // the user has not read the thread.
        $this->assertTrue($thread->hasUpdatesFor($user));


        // simulate that the use has read the thread
        $key = $user->visitedThreadCacheKey($thread);
        cache()->forever($key, Carbon::now());

        $this->assertFalse($thread->hasUpdatesFor($user));
    }

    /**
     * @return Thread
     */
    protected function getThread(): Thread
    {
        return $this->thread;
    }

    /**
     * @param Thread $thread
     *
     * @return ThreadTest
     */
    protected function setThread(Thread $thread): ThreadTest
    {
        $this->thread = $thread;

        return $this;
    }
}
