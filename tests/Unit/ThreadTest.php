<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Thread;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Collection;
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
    public function a_thread_has_replies()
    {
        $this->assertInstanceOf(Collection::class, $this->getThread()->replies);
    }

    /** @test */
    public function a_thread_as_a_creator()
    {
        $this->assertInstanceOf(user::class, $this->getThread()->creator);
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

    /**
     * @return Thread
     */
    public function getThread(): Thread
    {
        return $this->thread;
    }

    /**
     * @param Thread $thread
     *
     * @return ThreadTest
     */
    public function setThread(Thread $thread): ThreadTest
    {
        $this->thread = $thread;

        return $this;
    }
}
