<?php declare(strict_types=1);

namespace Tests\Feature;

use App\Thread;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_notification_is_prepared_when_a_subscribed_thread_receives_a_new_reply_that_is_not_by_the_current_user()
    {
        $this->signIn();

        /** @var Thread $thread */
        $thread = create(Thread::class)->subscribe();

        // before we add a reply, we should not have any notifications
        $this->assertCount(0, auth()->user()->notifications);

        // for the logged in user
        // then, each time a new a reply is left
        $thread->addReply([
           'user_id' => auth()->id(),
           'body' => 'Some reply here.',
        ]);

        /** @var User $myUser */
        $myUser = auth()->user();
        $myUser = $myUser->fresh();

        // logged in user does not get notification for their own thread.
        $this->assertCount(0, $myUser->notifications);

        // for a non logged in user.
        // then, each time a new a reply is left
        $thread->addReply([
            'user_id' => create(User::class)->id,
            'body' => 'Some reply here.',
        ]);

        /** @var User $myUser */
        $myUser = auth()->user();
        $myUser = $myUser->fresh();

        // non logged in user should get a notification.
        $this->assertCount(1, $myUser->notifications);
    }

    /** @test */
    public function a_user_can_fetch_their_unread_notifications()
    {
        $this->signIn();

        /** @var Thread $thread */
        $thread = create(Thread::class)->subscribe();

        $thread->addReply([
            'user_id' => create(User::class)->id,
            'body' => 'Some reply here.',
        ]);

        $response = $this->getJson('/profiles/'.auth()->user()->name.'/notifications')->json();

        $this->assertCount(1, $response);
    }

    /** @test */
    public function a_user_can_mark_a_notification_as_read()
    {
        $this->signIn();

        /** @var Thread $thread */
        $thread = create(Thread::class)->subscribe();

        $thread->addReply([
            'user_id' => create(User::class)->id,
            'body' => 'Some reply here.',
        ]);

        /** @var User $myUser */
        $myUser = auth()->user();

        $myUser = $myUser->unreadNotifications();
        $myUser = $myUser->get();

        // check that notifications are added.
        $this->assertCount(1, $myUser);

        // get notification id
        $notificationId = auth()->user()->unreadNotifications->first()->id;

        // now delete the notification
        $this->delete('/profiles/'.auth()->user()->name."/notifications/{$notificationId}");

        /** @var User $myUser */
        $myUser = auth()->user();
        $myUser = $myUser->fresh();

        $myUser = $myUser->unreadNotifications();
        $myUser = $myUser->get();

        // check to make sure the notification was deleted.
        $this->assertCount(0, $myUser);
    }
}
