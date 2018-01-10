<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Reply;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Throwable;

class FavoritesTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function guest_can_not_favorite_anything()
    {
        $this->withExceptionHandling()
            ->post('replies/1/favorites')
            ->assertRedirect('/login');
    }

    /** @test */
    public function an_authorized_user_can_favorite_any_reply()
    {
        $this->signIn();

        /** @var Reply $reply */
        $reply = create(Reply::class);

        $this->post('replies/'.$reply->id.'/favorites');

        $this->assertSame(1, $reply->favorites()->count());
    }

    /** @test */
    public function an_authenticated_user_may_only_favorite_a_reply_once()
    {
        $this->signIn();

        /** @var Reply $reply */
        $reply = create(Reply::class);

        try {
            $this->post('replies/'.$reply->id.'/favorites');
            $this->post('replies/'.$reply->id.'/favorites');
        } catch (throwable $t) {
            if ($t->getCode() === '23000') {
                $this->fail('Cannot insert duplicate favorites');
            } else {
                $this->fail('uncaught failure in an_authenticated_user_may_only_favorite_a_reply_once()');
            }
        }

        $this->assertSame(1, $reply->favorites()->count());
    }
}
