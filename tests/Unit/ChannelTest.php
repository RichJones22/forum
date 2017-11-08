<?php declare(strict_types=1);

namespace Tests\Feature;

use App\Channel;
use App\Thread;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

/**
 * Class ChannelTest.
 */
class ChannelTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_channel_consists_of_threads()
    {
        $channel = create(Channel::class);

        $thread = create(Thread::class, ['channel_id' => $channel->id]);

        $this->assertTrue($channel->threads->contains($thread));
    }
}
