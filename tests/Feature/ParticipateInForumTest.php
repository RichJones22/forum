<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Reply;
use App\Thread;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

/**
 * Class ParticipateInForumTest.
 */
class ParticipateInForumTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function unauthenticated_users_may_not_add_replies()
    {
        $this->expectException('Illuminate\Auth\AuthenticationException');

        $this->post('threads/1/replies', []);

//        $thread = factory(Thread::class)->create();
//
//        /** @var Reply $reply */
//        $reply = factory(Reply::class)->create();
//        $this->post($thread->path(). '/replies', $reply->toArray());
    }

    /** @test */
    public function an_authenticated_user_may_participate_in_forum_threads()
    {
        $this->be($user = create(User::class)); // create persists the db
                                                     // make does not...

        /** @var Thread $thread */
        $thread = create(Thread::class);

        /** @var Reply $reply */
        $reply = factory(Reply::class)->make();  // just make a reply... is not persisted to db...
        $this->post($thread->path().'/replies', $reply->toArray());

        $this->get($thread->path())
            ->assertSee($reply->body);
    }
}
