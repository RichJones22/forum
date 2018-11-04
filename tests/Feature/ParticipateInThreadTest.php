<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Reply;
use App\Thread;
use Exception;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

/**
 * Class ParticipateInThreadTest.
 */
class ParticipateInThreadTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function unauthenticated_users_may_not_add_replies()
    {
        $this->withExceptionHandling()
            ->post('threads/some-channel/1/replies', [])
            ->assertRedirect('/login');
    }

    /** @test */
    public function an_authenticated_user_may_participate_in_forum_threads()
    {
        $this->signIn();

        /** @var Thread $thread */
        $thread = create(Thread::class);

        /** @var Reply $reply */
        $reply = make(Reply::class);  // just make a reply... is not persisted to db...

        $this->post($thread->path().'/replies', $reply->toArray());

        $this->assertDatabaseHas('replies', ['body' => $reply->body]);
        $this->assertSame(1, (int)$thread->refresh()->getRepliesCount());
    }

    /** @test */
    public function a_reply_requires_a_body()
    {
        $this->withExceptionHandling()
            ->signIn();

        /** @var Thread $thread */
        $thread = create(Thread::class);

        /** @var Reply $reply */
        $reply = make(Reply::class, ['body' => null]);

        $this->post($thread->path().'/replies', $reply->toArray())
            ->assertSessionHasErrors('body');
    }

    /** @test */
    public function unauthorized_users_cannot_delete_replies()
    {
        $this->withExceptionHandling();

        $reply = create(Reply::class);

        $this->delete("/replies/{$reply->id}")
            ->assertRedirect('login');

        $this->signIn()
            ->delete("/replies/{$reply->id}")
            ->assertStatus(403);
    }

    /** @test */
    public function authorized_users_can_delete_replies()
    {
        $this->signIn();

        /** @var Reply $reply */
        $reply = create(Reply::class, ['user_id' => auth()->id()]);

        $this->delete("/replies/{$reply->id}")->assertStatus(302);

        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);

        /** @var Thread $model */
        $model = $reply->thread()->getModel();

        $this->assertNull($model->getRepliesCount());
    }

    /** @test */
    public function authorized_users_can_update_replies()
    {
        $bodyText = 'You been changed fool.';

        $this->signIn();

        $reply = create(Reply::class, ['user_id' => auth()->id(), 'body' => $bodyText]);

        $this->patch("/replies/{$reply->id}");

        $this->assertDatabaseHas('replies', ['id' => $reply->id, 'body' => $bodyText]);
    }

    /** @test */
    public function unauthorized_users_cannot_update_replies()
    {
        $this->withExceptionHandling();

        $reply = create(Reply::class);

        $this->patch("/replies/{$reply->id}")
            ->assertRedirect('login');

        $this->signIn()
            ->patch("/replies/{$reply->id}")
            ->assertStatus(403);
    }

    /** @test */
    public function replies_that_contain_spam_may_no_be_created()
    {
        $this->signIn();

        /** @var Thread $thread */
        $thread = create(Thread::class);

        /** @var Reply $reply */
        $reply = make(Reply::class, [
            'body' => 'Yahoo Customer Support',
//            'body' => 'bob Customer Support'
        ]);

        $this->expectException(Exception::class);

        $this->post($thread->path().'/replies', $reply->toArray());
    }
}
