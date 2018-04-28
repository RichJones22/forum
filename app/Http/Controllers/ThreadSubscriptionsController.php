<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Thread;

/**
 * Class ThreadSubscriptionsController.
 */
class ThreadSubscriptionsController extends Controller
{
    /**
     * @param $channelId
     * @param Thread $thread
     */
    public function store($channelId, Thread $thread)
    {
        $thread->subscribe();
    }

    public function destroy($channelId, Thread $thread)
    {
        $thread->unsubscribe();
    }
}
