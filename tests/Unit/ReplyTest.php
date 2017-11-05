<?php declare(strict_types=1);

namespace Tests\Unit;

use App\Reply;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

/**
 * Class ReplyTest.
 */
class ReplyTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_has_an_owner()
    {
        $reply = create(Reply::class);

        $this->assertInstanceOf(User::class, $reply->owner);
    }
}
